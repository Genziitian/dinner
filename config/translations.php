<?php
/**
 * Translation Dictionary for Atta Mill System
 * Supports English (en) and Hindi (hi)
 * Strictly NO emojis used
 */

declare(strict_types=1);

function get_mill_translations(): array {
    return [
        'en' => [
            // Navigation
            'nav_dashboard' => 'Dashboard',
            'nav_new_order' => 'New Order',
            'nav_orders'    => 'Orders',
            'nav_customers' => 'Customers',
            'nav_services'  => 'Rates',
            'nav_backup'    => 'Backup',
            'nav_logout'    => 'Logout',

            // Dashboard
            'dashboard_title'    => 'Mill Dashboard',
            'today_summary'      => "Today's Summary",
            'todays_orders'      => "Today's Orders",
            'pending_orders'     => 'In Process',
            'ready_orders'       => 'Ready for Pickup',
            'delivered_orders'   => 'Delivered',
            'total_sales'        => "Today's Total",
            'paid_amount'        => 'Paid Amount',
            'unpaid_amount'      => 'Pending Dues',
            'total_weight'       => "Today's Grinding (KG)",
            'quick_new_order'    => 'Create Grinding Order',
            'view_unpaid'        => 'View Unpaid Orders',
            'recent_orders'      => 'Recent Orders',

            // New Order
            'create_order_title' => 'Create New Grinding Order',
            'customer_name'      => 'Customer Name',
            'customer_phone'     => 'Mobile / WhatsApp Number',
            'service_type'       => 'Select Service',
            'weight_kg'          => 'Weight (in KG)',
            'rate_per_kg'        => 'Rate per KG',
            'total_price'        => 'Total Amount',
            'payment_status'     => 'Payment Status',
            'payment_method'     => 'Payment Mode',
            'cash'               => 'Cash',
            'upi'                => 'UPI / Online',
            'optional_notes'     => 'Notes (Optional)',
            'notes_placeholder'  => 'e.g. Fine grinding, packing instructions...',
            'save_order_btn'     => 'Confirm & Save Order',
            'existing_customer'  => 'Existing Customer',

            // Statuses
            'status_all'        => 'All Orders',
            'status_received'   => 'Received',
            'status_processing' => 'Processing',
            'status_ready'      => 'Ready',
            'status_delivered'  => 'Delivered',
            'status_cancelled'  => 'Cancelled',
            'status_unpaid'     => 'Unpaid Orders',

            // Payment states
            'paid'   => 'Paid',
            'unpaid' => 'Unpaid',

            // Orders list
            'order_list_title' => 'Order Records',
            'search_placeholder' => 'Search by customer name, phone, order #...',
            'order_no'         => 'Order #',
            'service'          => 'Service',
            'weight'           => 'Weight',
            'rate'             => 'Rate',
            'amount'           => 'Amount',
            'status'           => 'Status',
            'payment'          => 'Payment',
            'actions'          => 'Actions',
            'no_orders'        => 'No orders found for this criteria.',
            'mark_paid'        => 'Mark as Paid',
            'mark_unpaid'      => 'Mark as Unpaid',

            // WhatsApp
            'whatsapp_btn'       => 'WhatsApp',
            'whatsapp_modal_title' => 'Send WhatsApp Message',
            'whatsapp_edit_hint' => 'You can edit this message before sending:',
            'send_via_whatsapp'  => 'Open WhatsApp',
            'msg_received'       => 'Namaste {customer}, your order #{order_num} for {service} ({weight} KG) has been received at {shop}. Total: Rs.{amount}. We will notify you once ready.',
            'msg_ready'          => 'Namaste {customer}, your order #{order_num} for {service} ({weight} KG) is now READY for pickup at {shop}. Amount: Rs.{amount} ({payment}). Please collect.',

            // Customers
            'customer_title'     => 'Customer Records',
            'search_customer'    => 'Search customer name or mobile...',
            'total_visits'       => 'Total Orders',
            'total_spent'        => 'Total Amount',
            'pending_due'        => 'Pending Due',
            'history'            => 'History',
            'customer_history'   => 'Customer Grinding History',
            'no_customers'       => 'No customer records found.',

            // Services & Rates
            'services_title'     => 'Services & Rate Management',
            'add_new_service'    => 'Add New Service',
            'service_name'       => 'Service Name',
            'service_name_hi'    => 'Hindi Name (Optional)',
            'rate_fixed'         => 'Price per KG (Rs.)',
            'active'             => 'Active',
            'inactive'           => 'Inactive',
            'edit_service'       => 'Edit Service',
            'save_service'       => 'Save Service',
            'disable'            => 'Disable',
            'enable'             => 'Enable',

            // Backup
            'backup_title'       => 'Data Backup & Restore',
            'export_title'       => 'Export Business Records',
            'export_desc'        => 'Download a complete copy of all customer records, grinding orders, and services in JSON format.',
            'download_backup'    => 'Download Backup File',
            'restore_title'      => 'Restore from Backup',
            'restore_desc'       => 'Upload a previously saved backup file to restore records. Existing duplicate orders will be safely skipped.',
            'choose_file'        => 'Choose Backup JSON File',
            'upload_restore_btn' => 'Upload & Restore',

            // Common
            'cancel'       => 'Cancel',
            'close'        => 'Close',
            'date'         => 'Date',
            'time'         => 'Time',
            'notes'        => 'Notes',
            'lang_en'      => 'English',
            'lang_hi'      => 'हिन्दी',
        ],

        'hi' => [
            // Navigation
            'nav_dashboard' => 'डैशबोर्ड',
            'nav_new_order' => 'नया ऑर्डर',
            'nav_orders'    => 'ऑर्डर सूची',
            'nav_customers' => 'ग्राहक',
            'nav_services'  => 'दर सूची',
            'nav_backup'    => 'बैकअप',
            'nav_logout'    => 'लॉगआउट',

            // Dashboard
            'dashboard_title'    => 'मिल डैशबोर्ड',
            'today_summary'      => 'आज का सारांश',
            'todays_orders'      => 'आज के कुल ऑर्डर',
            'pending_orders'     => 'प्रक्रिया में',
            'ready_orders'       => 'तैयार ऑर्डर',
            'delivered_orders'   => 'डिलीवर हुए',
            'total_sales'        => 'आज की कुल राशि',
            'paid_amount'        => 'प्राप्त राशि',
            'unpaid_amount'      => 'बाकी (उधार)',
            'total_weight'       => 'आज की कुल पिसाई (किलो)',
            'quick_new_order'    => 'नया पिसाई ऑर्डर जोड़ें',
            'view_unpaid'        => 'उधार वाले ऑर्डर देखें',
            'recent_orders'      => 'हाल के ऑर्डर',

            // New Order
            'create_order_title' => 'नया पिसाई ऑर्डर बनाएं',
            'customer_name'      => 'ग्राहक का नाम',
            'customer_phone'     => 'मोबाइल / व्हाट्सएप नंबर',
            'service_type'       => 'सेवा का प्रकार चुनें',
            'weight_kg'          => 'वजन (किलो में)',
            'rate_per_kg'        => 'दर प्रति किलो (रु)',
            'total_price'        => 'कुल राशि',
            'payment_status'     => 'भुगतान स्थिति',
            'payment_method'     => 'भुगतान का माध्यम',
            'cash'               => 'नकद (Cash)',
            'upi'                => 'ऑनलाइन / UPI',
            'optional_notes'     => 'विशेष निर्देश (वैकल्पिक)',
            'notes_placeholder'  => 'जैसे: बारीक पिसाई, चोकर अलग रखना...',
            'save_order_btn'     => 'ऑर्डर सुरक्षित करें',
            'existing_customer'  => 'पुराना ग्राहक',

            // Statuses
            'status_all'        => 'सभी ऑर्डर',
            'status_received'   => 'प्राप्त हुआ',
            'status_processing' => 'प्रक्रिया में',
            'status_ready'      => 'तैयार है',
            'status_delivered'  => 'डिलीवर हुआ',
            'status_cancelled'  => 'रद्द हुआ',
            'status_unpaid'     => 'उधार वाले ऑर्डर',

            // Payment states
            'paid'   => 'जमा (Paid)',
            'unpaid' => 'बाकी (Unpaid)',

            // Orders list
            'order_list_title' => 'ऑर्डर रिकॉर्ड',
            'search_placeholder' => 'ग्राहक का नाम, मोबाइल नंबर या ऑर्डर # खोजें...',
            'order_no'         => 'ऑर्डर #',
            'service'          => 'सेवा',
            'weight'           => 'वजन',
            'rate'             => 'दर',
            'amount'           => 'राशि',
            'status'           => 'स्थिति',
            'payment'          => 'भुगतान',
            'actions'          => 'कार्रवाई',
            'no_orders'        => 'इस श्रेणी में कोई ऑर्डर नहीं मिला।',
            'mark_paid'        => 'भुगतान प्राप्त हुआ करें',
            'mark_unpaid'      => 'बाकी (उधार) करें',

            // WhatsApp
            'whatsapp_btn'       => 'व्हाट्सएप',
            'whatsapp_modal_title' => 'व्हाट्सएप संदेश भेजें',
            'whatsapp_edit_hint' => 'भेजने से पहले संदेश संपादित कर सकते हैं:',
            'send_via_whatsapp'  => 'व्हाट्सएप खोलें',
            'msg_received'       => 'नमस्ते {customer}, {shop} पर आपका {service} ({weight} किलो) का ऑर्डर #{order_num} प्राप्त हो गया है। कुल राशि: Rs.{amount}। तैयार होने पर सूचित करेंगे।',
            'msg_ready'          => 'नमस्ते {customer}, {shop} पर आपका {service} ({weight} किलो) का ऑर्डर #{order_num} तैयार है! कृपया आकर ले जाएं। कुल राशि: Rs.{amount} ({payment})।',

            // Customers
            'customer_title'     => 'ग्राहक रिकॉर्ड',
            'search_customer'    => 'ग्राहक का नाम या मोबाइल खोजें...',
            'total_visits'       => 'कुल ऑर्डर',
            'total_spent'        => 'कुल कारोबार',
            'pending_due'        => 'कुल बाकी (उधार)',
            'history'            => 'इतिहास',
            'customer_history'   => 'ग्राहक का पिसाई इतिहास',
            'no_customers'       => 'कोई ग्राहक रिकॉर्ड नहीं मिला।',

            // Services & Rates
            'services_title'     => 'सेवाएं एवं दर प्रबंधन',
            'add_new_service'    => 'नई सेवा जोड़ें',
            'service_name'       => 'सेवा का नाम',
            'service_name_hi'    => 'हिंदी में नाम',
            'rate_fixed'         => 'दर प्रति किलो (रु)',
            'active'             => 'सक्रिय',
            'inactive'           => 'निष्क्रिय',
            'edit_service'       => 'संपादित करें',
            'save_service'       => 'सुरक्षित करें',
            'disable'            => 'बंद करें',
            'enable'             => 'चालू करें',

            // Backup
            'backup_title'       => 'डेटा बैकअप एवं रिस्टोर',
            'export_title'       => 'रिकॉर्ड बैकअप डाउनलोड करें',
            'export_desc'        => 'सभी ग्राहकों, पिसाई ऑर्डरों और सेवाओं का पूरा डेटा JSON फाइल के रूप में सुरक्षित डाउनलोड करें।',
            'download_backup'    => 'बैकअप फाइल डाउनलोड करें',
            'restore_title'      => 'बैकअप से डेटा रिस्टोर करें',
            'restore_desc'       => 'पहले डाउनलोड की गई बैकअप फाइल अपलोड करें। पुराने रिकॉर्ड दोबारा नहीं जुड़ेंगे।',
            'choose_file'        => 'बैकअप फाइल चुनें',
            'upload_restore_btn' => 'अपलोड और रिस्टोर करें',

            // Common
            'cancel'       => 'रद्द करें',
            'close'        => 'बंद करें',
            'date'         => 'दिनांक',
            'time'         => 'समय',
            'notes'        => 'नोट',
            'lang_en'      => 'English',
            'lang_hi'      => 'हिन्दी',
        ],
    ];
}

/**
 * Translation helper function
 */
function __t(string $key, array $replace = []): string {
    $lang = current_lang();
    $translations = get_mill_translations();
    $text = $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;

    if (!empty($replace)) {
        foreach ($replace as $placeholder => $value) {
            $text = str_replace('{' . $placeholder . '}', (string)$value, $text);
        }
    }

    return $text;
}

/**
 * Get current selected language
 */
function current_lang(): string {
    startSecureSession();
    return $_SESSION['app_lang'] ?? $_COOKIE['app_lang'] ?? 'en';
}

/**
 * Set selected language
 */
function set_lang(string $lang): void {
    startSecureSession();
    $clean = in_array($lang, ['en', 'hi'], true) ? $lang : 'en';
    $_SESSION['app_lang'] = $clean;
    if (php_sapi_name() !== 'cli' && !headers_sent()) {
        setcookie('app_lang', $clean, time() + 86400 * 365, '/');
    }
}
