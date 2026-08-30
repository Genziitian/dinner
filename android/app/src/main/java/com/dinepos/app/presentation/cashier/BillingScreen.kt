package com.dinepos.app.presentation.cashier

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.QrCode
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.ShoppingCart
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.Order
import com.dinepos.app.presentation.cashier.components.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BillingScreen(
    onOrderPlaced: (Order) -> Unit,
    onNavigateToScanner: () -> Unit = {},
    viewModel: CashierViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val context = LocalContext.current

    LaunchedEffect(key1 = true) {
        viewModel.events.collect { event ->
            when (event) {
                is CashierEvent.OrderSubmittedSuccess -> {
                    Toast.makeText(context, "Order #${event.order.orderNumber} Saved!", Toast.LENGTH_SHORT).show()
                    onOrderPlaced(event.order)
                }
                is CashierEvent.ShowToast -> {
                    Toast.makeText(context, event.message, Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    BoxWithConstraints(modifier = Modifier.fillMaxSize()) {
        val isTabletLayout = maxWidth >= 600.dp

        Scaffold(
            containerColor = BrandBackground,
            topBar = {
                TopAppBar(
                    title = {
                        Column {
                            Text(
                                text = uiState.restaurantName.ifBlank { "Menu Catalog" },
                                style = MaterialTheme.typography.titleLarge,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                            Text(
                                text = "${uiState.items.size} items available",
                                style = MaterialTheme.typography.bodySmall,
                                color = TextSecondary
                            )
                        }
                    },
                    actions = {
                        IconButton(onClick = onNavigateToScanner) {
                            Icon(
                                imageVector = Icons.Default.QrCode,
                                contentDescription = "Scan Receipt QR",
                                tint = BrandOrange
                            )
                        }

                        // Next Order # Badge
                        Surface(
                            shape = CircleShape,
                            color = BrandDark,
                            modifier = Modifier.padding(end = 4.dp)
                        ) {
                            Row(
                                modifier = Modifier.padding(horizontal = 10.dp, vertical = 6.dp),
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(6.dp)
                            ) {
                                Box(modifier = Modifier.size(8.dp).clip(CircleShape).background(BrandAmber))
                                Text(
                                    text = "Order #${uiState.previewOrderNumber}",
                                    color = Color.White,
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 12.sp
                                )
                            }
                        }

                        IconButton(onClick = { viewModel.loadMenu() }) {
                            Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = BrandDark)
                        }
                    },
                    colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
                )
            },
            bottomBar = {
                // Cart Bottom Bar (Only on phones/compact screens)
                if (!isTabletLayout) {
                    val cartTotal = uiState.cartItems.sumOf { it.lineTotal }

                    Surface(
                        color = BrandSurface,
                        shadowElevation = 12.dp,
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(14.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Row(
                                modifier = Modifier.clickable { viewModel.setShowCartBottomSheet(true) },
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(10.dp)
                            ) {
                                BadgedBox(
                                    badge = {
                                        if (uiState.cartItems.isNotEmpty()) {
                                            Badge(containerColor = BrandOrange) {
                                                Text(text = "${uiState.cartItems.size}", fontWeight = FontWeight.Bold)
                                            }
                                        }
                                    }
                                ) {
                                    Icon(
                                        imageVector = Icons.Default.ShoppingCart,
                                        contentDescription = "Cart",
                                        tint = BrandDark,
                                        modifier = Modifier.size(28.dp)
                                    )
                                }

                                Column {
                                    Text(
                                        text = "Total: ${CurrencyFormatter.formatInr(cartTotal)}",
                                        style = MaterialTheme.typography.titleMedium,
                                        fontWeight = FontWeight.ExtraBold,
                                        color = BrandDark
                                    )
                                    Text(
                                        text = if (uiState.cartItems.isEmpty()) "Cart is empty" else "${uiState.cartItems.size} items in cart",
                                        style = MaterialTheme.typography.bodySmall,
                                        color = TextSecondary
                                    )
                                }
                            }

                            Button(
                                onClick = { viewModel.setShowCartBottomSheet(true) },
                                enabled = uiState.cartItems.isNotEmpty(),
                                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                                shape = RoundedCornerShape(12.dp),
                                contentPadding = PaddingValues(horizontal = 20.dp, vertical = 10.dp)
                            ) {
                                Text(
                                    text = "View Cart",
                                    fontWeight = FontWeight.Bold,
                                    color = Color.White
                                )
                            }
                        }
                    }
                }
            }
        ) { paddingValues ->
            if (isTabletLayout) {
                // Tablet Dual-Pane Side-by-Side POS Layout
                Row(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues)
                        .padding(16.dp),
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Left Pane: Catalog, Search & Categories
                    Column(
                        modifier = Modifier
                            .weight(1f)
                            .fillMaxHeight()
                    ) {
                        MenuSearchAndFilter(
                            searchQuery = uiState.searchQuery,
                            onSearchQueryChange = { viewModel.onSearchQueryChange(it) },
                            selectedCategory = uiState.selectedCategory,
                            onCategorySelect = { viewModel.onCategorySelect(it) }
                        )

                        Spacer(modifier = Modifier.height(12.dp))

                        MenuItemsGrid(
                            isLoading = uiState.isLoading,
                            searchQuery = uiState.searchQuery,
                            filteredItems = uiState.filteredItems,
                            minGridSize = 135.dp,
                            onItemClick = { viewModel.onItemClicked(it) }
                        )
                    }

                    // Right Pane: Permanent Live Cart Panel
                    CartPanel(
                        cartItems = uiState.cartItems,
                        customerName = uiState.customerName,
                        customerPhone = uiState.customerPhone,
                        selectedPayment = uiState.selectedPayment,
                        previewOrderNumber = uiState.previewOrderNumber,
                        isSubmitting = uiState.isSubmitting,
                        onCustomerNameChange = { viewModel.onCustomerNameChange(it) },
                        onCustomerPhoneChange = { viewModel.onCustomerPhoneChange(it) },
                        onPaymentMethodChange = { viewModel.onPaymentMethodChange(it) },
                        onUpdateQuantity = { index, delta -> viewModel.updateCartItemQuantity(index, delta) },
                        onRemoveItem = { index -> viewModel.removeCartItem(index) },
                        onClearCart = { viewModel.clearCart() },
                        onSubmitOrder = { viewModel.submitOrder() },
                        modifier = Modifier
                            .widthIn(min = 340.dp, max = 440.dp)
                            .fillMaxHeight()
                    )
                }
            } else {
                // Phone Single-Column Layout
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues)
                        .padding(horizontal = 16.dp, vertical = 8.dp)
                ) {
                    MenuSearchAndFilter(
                        searchQuery = uiState.searchQuery,
                        onSearchQueryChange = { viewModel.onSearchQueryChange(it) },
                        selectedCategory = uiState.selectedCategory,
                        onCategorySelect = { viewModel.onCategorySelect(it) }
                    )

                    Spacer(modifier = Modifier.height(12.dp))

                    MenuItemsGrid(
                        isLoading = uiState.isLoading,
                        searchQuery = uiState.searchQuery,
                        filteredItems = uiState.filteredItems,
                        minGridSize = 150.dp,
                        onItemClick = { viewModel.onItemClicked(it) }
                    )
                }
            }
        }

        // Interactive Dialogs for Item Selection
        val activeItem = uiState.activeItemForModal
        if (activeItem != null) {
            when (activeItem.itemType) {
                "portion" -> {
                    PortionDialog(
                        item = activeItem,
                        onDismiss = { viewModel.dismissItemModal() },
                        onSelectVariant = { variant ->
                            viewModel.addVariantToCart(activeItem, variant, 1.0)
                        }
                    )
                }
                "piece" -> {
                    val variant = activeItem.variants.firstOrNull { it.active } ?: activeItem.variants.firstOrNull()
                    if (variant != null) {
                        PieceDialog(
                            item = activeItem,
                            variant = variant,
                            onDismiss = { viewModel.dismissItemModal() },
                            onConfirm = { qty ->
                                viewModel.addVariantToCart(activeItem, variant, qty)
                            }
                        )
                    }
                }
                "weight" -> {
                    val variant = activeItem.variants.firstOrNull { it.active } ?: activeItem.variants.firstOrNull()
                    if (variant != null) {
                        WeightDialog(
                            item = activeItem,
                            variant = variant,
                            onDismiss = { viewModel.dismissItemModal() },
                            onConfirm = { qty ->
                                viewModel.addVariantToCart(activeItem, variant, qty)
                            }
                        )
                    }
                }
                else -> {
                    val variant = activeItem.variants.firstOrNull { it.active } ?: activeItem.variants.firstOrNull()
                    if (variant != null) {
                        viewModel.addVariantToCart(activeItem, variant, 1.0)
                    }
                }
            }
        }

        // Active Cart Bottom Sheet (Only on phone layout)
        if (!isTabletLayout && uiState.showCartBottomSheet) {
            CartBottomSheet(
                cartItems = uiState.cartItems,
                customerName = uiState.customerName,
                customerPhone = uiState.customerPhone,
                selectedPayment = uiState.selectedPayment,
                previewOrderNumber = uiState.previewOrderNumber,
                isSubmitting = uiState.isSubmitting,
                onCustomerNameChange = { viewModel.onCustomerNameChange(it) },
                onCustomerPhoneChange = { viewModel.onCustomerPhoneChange(it) },
                onPaymentMethodChange = { viewModel.onPaymentMethodChange(it) },
                onUpdateQuantity = { index, delta -> viewModel.updateCartItemQuantity(index, delta) },
                onRemoveItem = { index -> viewModel.removeCartItem(index) },
                onClearCart = { viewModel.clearCart() },
                onSubmitOrder = { viewModel.submitOrder() },
                onDismiss = { viewModel.setShowCartBottomSheet(false) }
            )
        }
    }
}

@Composable
private fun MenuSearchAndFilter(
    searchQuery: String,
    onSearchQueryChange: (String) -> Unit,
    selectedCategory: String,
    onCategorySelect: (String) -> Unit
) {
    // Search Input
    OutlinedTextField(
        value = searchQuery,
        onValueChange = onSearchQueryChange,
        placeholder = { Text("Search menu item (e.g. Curry, Egg, Rice)...") },
        leadingIcon = { Icon(Icons.Default.Search, contentDescription = null, tint = TextMuted) },
        singleLine = true,
        shape = RoundedCornerShape(14.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedContainerColor = BrandSurface,
            unfocusedContainerColor = BrandSurface
        ),
        modifier = Modifier.fillMaxWidth()
    )

    Spacer(modifier = Modifier.height(10.dp))

    // Category Filter Chips
    val categories = listOf(
        "all" to "🍽️ All Items",
        "portion" to "🍗 Portion Meals",
        "piece" to "🥚 Per Piece",
        "weight" to "🌾 By Weight / Vol"
    )

    LazyRow(
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        items(categories) { (catKey, catLabel) ->
            val isSelected = selectedCategory == catKey
            Surface(
                shape = RoundedCornerShape(20.dp),
                color = if (isSelected) BrandDark else BrandSurface,
                border = BorderStroke(1.dp, if (isSelected) BrandDark else BrandBorder),
                modifier = Modifier.clickable { onCategorySelect(catKey) }
            ) {
                Text(
                    text = catLabel,
                    color = if (isSelected) Color.White else TextSecondary,
                    fontWeight = FontWeight.Bold,
                    fontSize = 13.sp,
                    modifier = Modifier.padding(horizontal = 14.dp, vertical = 8.dp)
                )
            }
        }
    }
}

@Composable
private fun MenuItemsGrid(
    isLoading: Boolean,
    searchQuery: String,
    filteredItems: List<com.dinepos.app.domain.model.MenuItem>,
    minGridSize: androidx.compose.ui.unit.Dp,
    onItemClick: (com.dinepos.app.domain.model.MenuItem) -> Unit
) {
    if (isLoading) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = BrandOrange)
        }
    } else if (filteredItems.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text(
                text = if (searchQuery.isNotBlank()) "No items match your search." else "No active menu items found.",
                color = TextSecondary,
                style = MaterialTheme.typography.bodyLarge
            )
        }
    } else {
        LazyVerticalGrid(
            columns = GridCells.Adaptive(minSize = minGridSize),
            horizontalArrangement = Arrangement.spacedBy(10.dp),
            verticalArrangement = Arrangement.spacedBy(10.dp),
            modifier = Modifier.fillMaxSize()
        ) {
            items(filteredItems) { item ->
                ItemGridCard(
                    item = item,
                    onClick = { onItemClick(item) }
                )
            }
        }
    }
}
