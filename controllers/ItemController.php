<?php
/**
 * Item Management Controller (Manager Only)
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Item.php';
require_once ROOT_PATH . '/models/ItemVariant.php';
require_once ROOT_PATH . '/models/AuditLog.php';

class ItemController extends BaseController {
    /**
     * List all items for the restaurant
     */
    public function index(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $items = Item::allByRestaurant($restaurantId, false);

        $this->render('manager/items', [
            'title' => 'Menu Items | DinePOS',
            'items' => $items,
        ]);
    }

    /**
     * Show create item form
     */
    public function create(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $this->render('manager/item_form', [
            'title' => 'Add Menu Item | DinePOS',
            'item' => null,
            'isEdit' => false,
        ]);
    }

    /**
     * Store newly created item
     */
    public function store(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $name = trim((string)($_POST['name'] ?? ''));
        $itemType = (string)($_POST['item_type'] ?? Item::TYPE_PIECE);
        $baseUnit = trim((string)($_POST['base_unit'] ?? 'piece'));
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($name)) {
            $this->redirect('/manager/items/create', 'danger', 'Item name is required.');
        }

        // Parse variants
        $variants = $this->extractVariantsFromPost($itemType, $baseUnit);

        if (empty($variants)) {
            $this->redirect('/manager/items/create', 'danger', 'At least one pricing variant is required.');
        }

        try {
            $itemId = Item::create(
                $restaurantId,
                [
                    'name' => $name,
                    'item_type' => $itemType,
                    'base_unit' => $baseUnit,
                    'active' => $active,
                ],
                $variants
            );

            AuditLog::log(
                AuditLog::ACTION_ITEM_CREATE,
                'item',
                $itemId,
                $restaurantId,
                $user['id'],
                ['name' => $name, 'item_type' => $itemType]
            );

            $this->redirect('/manager/items', 'success', "Item '{$name}' created successfully.");
        } catch (Throwable $e) {
            error_log('Item creation error: ' . $e->getMessage());
            $this->redirect('/manager/items/create', 'danger', 'Error creating item: ' . $e->getMessage());
        }
    }

    /**
     * Show edit item form
     */
    public function edit(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $itemId = (int)($_GET['id'] ?? 0);
        $item = Item::findById($itemId, $restaurantId);

        if (!$item) {
            $this->redirect('/manager/items', 'danger', 'Item not found or inaccessible.');
        }

        $this->render('manager/item_form', [
            'title' => "Edit Item - {$item['name']} | DinePOS",
            'item' => $item,
            'isEdit' => true,
        ]);
    }

    /**
     * Update existing item
     */
    public function update(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $itemId = (int)($_POST['id'] ?? 0);
        $item = Item::findById($itemId, $restaurantId);

        if (!$item) {
            $this->redirect('/manager/items', 'danger', 'Item not found or inaccessible.');
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $itemType = (string)($_POST['item_type'] ?? $item['item_type']);
        $baseUnit = trim((string)($_POST['base_unit'] ?? $item['base_unit']));
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($name)) {
            $this->redirect("/manager/items/edit?id={$itemId}", 'danger', 'Item name is required.');
        }

        $variants = $this->extractVariantsFromPost($itemType, $baseUnit);

        try {
            Item::update(
                $itemId,
                $restaurantId,
                [
                    'name' => $name,
                    'item_type' => $itemType,
                    'base_unit' => $baseUnit,
                    'active' => $active,
                ],
                $variants
            );

            AuditLog::log(
                AuditLog::ACTION_ITEM_EDIT,
                'item',
                $itemId,
                $restaurantId,
                $user['id'],
                ['name' => $name, 'active' => $active]
            );

            $this->redirect('/manager/items', 'success', "Item '{$name}' updated successfully.");
        } catch (Throwable $e) {
            error_log('Item update error: ' . $e->getMessage());
            $this->redirect("/manager/items/edit?id={$itemId}", 'danger', 'Error updating item: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active / inactive status
     */
    public function toggle(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $itemId = (int)($_POST['id'] ?? 0);
        $item = Item::findById($itemId, $restaurantId);

        if (!$item) {
            $this->redirect('/manager/items', 'danger', 'Item not found.');
        }

        Item::toggleActive($itemId, $restaurantId);
        $action = $item['active'] ? AuditLog::ACTION_ITEM_DEACTIVATE : AuditLog::ACTION_ITEM_REACTIVATE;

        AuditLog::log($action, 'item', $itemId, $restaurantId, $user['id']);

        $statusMsg = $item['active'] ? 'deactivated' : 'activated';
        $this->redirect('/manager/items', 'success', "Item '{$item['name']}' has been {$statusMsg}.");
    }

    /**
     * Helper to extract variants based on item type
     */
    private function extractVariantsFromPost(string $itemType, string $baseUnit): array {
        $variants = [];

        if ($itemType === Item::TYPE_PORTION) {
            // Full, Half, Quarter or custom portions
            $names = $_POST['variant_names'] ?? [];
            $prices = $_POST['variant_prices'] ?? [];
            $values = $_POST['variant_values'] ?? [];

            foreach ($names as $idx => $vName) {
                $price = $prices[$idx] ?? '';
                if ($price !== '' && is_numeric($price)) {
                    $variants[] = [
                        'variant_name' => trim($vName),
                        'quantity_value' => !empty($values[$idx]) ? (float)$values[$idx] : 1.0,
                        'quantity_unit' => 'portion',
                        'price' => (float)$price,
                        'active' => 1,
                    ];
                }
            }
        } elseif ($itemType === Item::TYPE_WEIGHT) {
            $price = $_POST['single_price'] ?? '';
            if ($price !== '' && is_numeric($price)) {
                $variants[] = [
                    'variant_name' => "1 {$baseUnit}",
                    'quantity_value' => 1.0,
                    'quantity_unit' => $baseUnit,
                    'price' => (float)$price,
                    'active' => 1,
                ];
            }
        } else {
            // Per Piece
            $price = $_POST['single_price'] ?? '';
            if ($price !== '' && is_numeric($price)) {
                $variants[] = [
                    'variant_name' => 'Standard',
                    'quantity_value' => 1.0,
                    'quantity_unit' => 'piece',
                    'price' => (float)$price,
                    'active' => 1,
                ];
            }
        }

        return $variants;
    }
}
