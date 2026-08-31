package com.dinepos.app.presentation.cashier

import android.widget.Toast
import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.core.*
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.scaleIn
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
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.ShoppingCart
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.Order
import com.dinepos.app.presentation.cashier.components.*
import kotlinx.coroutines.delay

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BillingScreen(
    onOrderPlaced: (Order) -> Unit,
    onNavigateToScanner: () -> Unit = {},
    onNavigateToOrders: () -> Unit = {},
    onNavigateBack: (() -> Unit)? = null,
    viewModel: CashierViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val context = LocalContext.current
    var successOrder by remember { mutableStateOf<Order?>(null) }

    LaunchedEffect(key1 = true) {
        viewModel.events.collect { event ->
            when (event) {
                is CashierEvent.OrderSubmittedSuccess -> {
                    successOrder = event.order
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
                Column {
                    TopAppBar(
                        navigationIcon = {
                            if (onNavigateBack != null) {
                                IconButton(onClick = onNavigateBack) {
                                    Icon(
                                        imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                                        contentDescription = "Back",
                                        tint = BrandDark
                                    )
                                }
                            }
                        },
                        title = {
                            Column {
                                Surface(
                                    shape = RoundedCornerShape(6.dp),
                                    color = BrandOrange.copy(alpha = 0.12f),
                                    border = BorderStroke(1.dp, BrandOrange.copy(alpha = 0.35f)),
                                    modifier = Modifier.padding(bottom = 2.dp)
                                ) {
                                    Row(
                                        modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp),
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Box(
                                            modifier = Modifier
                                                .size(6.dp)
                                                .background(BrandOrange, CircleShape)
                                        )
                                        Spacer(modifier = Modifier.width(4.dp))
                                        Text(
                                            text = "GI ORDER POS",
                                            fontSize = 9.sp,
                                            fontWeight = FontWeight.Black,
                                            color = BrandOrange,
                                            letterSpacing = 0.6.sp
                                        )
                                    }
                                }
                                Text(
                                    text = uiState.restaurantName.ifBlank { "Menu Catalog" },
                                    style = MaterialTheme.typography.titleLarge,
                                    fontWeight = FontWeight.ExtraBold,
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
                        colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White)
                    )
                    HorizontalDivider(color = BrandBorder.copy(alpha = 0.7f), thickness = 1.dp)
                }
            },
            bottomBar = {
                // Cart Bottom Bar (Only on phones/compact screens)
                if (!isTabletLayout) {
                    val cartTotal = uiState.cartItems.sumOf { it.lineTotal }

                    Surface(
                        color = Color.White,
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
            }
        }

        // Bottom Sheet Cart for Phone Form Factor
        if (!isTabletLayout && uiState.showCartBottomSheet) {
            CartBottomSheet(
                cartItems = uiState.cartItems,
                customerName = uiState.customerName,
                customerPhone = uiState.customerPhone,
                selectedPayment = uiState.selectedPayment,
                previewOrderNumber = uiState.previewOrderNumber,
                isSubmitting = uiState.isSubmitting,
                onDismiss = { viewModel.setShowCartBottomSheet(false) },
                onCustomerNameChange = { viewModel.onCustomerNameChange(it) },
                onCustomerPhoneChange = { viewModel.onCustomerPhoneChange(it) },
                onPaymentMethodChange = { viewModel.onPaymentMethodChange(it) },
                onUpdateQuantity = { index, delta -> viewModel.updateCartItemQuantity(index, delta) },
                onRemoveItem = { index -> viewModel.removeCartItem(index) },
                onClearCart = { viewModel.clearCart() },
                onSubmitOrder = { viewModel.submitOrder() }
            )
        }

        // Full Screen PhonePe Style Payment Success Animation Overlay
        val order = successOrder
        if (order != null) {
            PaymentSuccessOverlay(
                order = order,
                onComplete = {
                    val targetOrder = order
                    successOrder = null
                    onOrderPlaced(targetOrder)
                }
            )
        }
    }
}

@Composable
private fun PaymentSuccessOverlay(
    order: Order,
    onComplete: () -> Unit
) {
    // Auto navigate after 1.8 seconds
    LaunchedEffect(Unit) {
        delay(1800)
        onComplete()
    }

    val infiniteTransition = rememberInfiniteTransition(label = "pulse")
    val pulseScale by infiniteTransition.animateFloat(
        initialValue = 0.95f,
        targetValue = 1.12f,
        animationSpec = infiniteRepeatable(
            animation = tween(900, easing = FastOutSlowInEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "pulse_scale"
    )

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(
                    listOf(
                        Color(0xFF047857),
                        Color(0xFF065F46),
                        Color(0xFF0F172A)
                    )
                )
            )
            .clickable { onComplete() },
        contentAlignment = Alignment.Center
    ) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center,
            modifier = Modifier.padding(24.dp)
        ) {
            // Pulsing Green Glow Ring
            Box(
                modifier = Modifier
                    .size(140.dp)
                    .scale(pulseScale)
                    .clip(CircleShape)
                    .background(Color(0x3334D399)),
                contentAlignment = Alignment.Center
            ) {
                Box(
                    modifier = Modifier
                        .size(105.dp)
                        .clip(CircleShape)
                        .background(Color(0xFF10B981)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.Check,
                        contentDescription = "Success",
                        tint = Color.White,
                        modifier = Modifier.size(64.dp)
                    )
                }
            }

            Spacer(modifier = Modifier.height(28.dp))

            Text(
                text = "Order Placed Successfully!",
                color = Color.White,
                fontSize = 24.sp,
                fontWeight = FontWeight.ExtraBold,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(8.dp))

            Text(
                text = CurrencyFormatter.formatInr(order.total),
                color = Color(0xFF6EE7B7),
                fontSize = 36.sp,
                fontWeight = FontWeight.Black,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(8.dp))

            Surface(
                shape = RoundedCornerShape(20.dp),
                color = Color(0x33FFFFFF),
                modifier = Modifier.padding(top = 4.dp)
            ) {
                Row(
                    modifier = Modifier.padding(horizontal = 14.dp, vertical = 6.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Order #${order.orderNumber} • ${order.paymentMethod.uppercase()}",
                        color = Color.White,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }

            Spacer(modifier = Modifier.height(36.dp))

            Button(
                onClick = onComplete,
                colors = ButtonDefaults.buttonColors(containerColor = Color.White),
                shape = RoundedCornerShape(14.dp),
                contentPadding = PaddingValues(horizontal = 24.dp, vertical = 12.dp)
            ) {
                Text(
                    text = "View Receipt ➔",
                    color = Color(0xFF047857),
                    fontWeight = FontWeight.ExtraBold,
                    fontSize = 15.sp
                )
            }
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
        placeholder = { Text("Search menu item (e.g. Curry, Egg, Rice)...", color = TextMuted) },
        leadingIcon = { Icon(Icons.Default.Search, contentDescription = null, tint = TextMuted) },
        singleLine = true,
        shape = RoundedCornerShape(14.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedContainerColor = Color.White,
            unfocusedContainerColor = Color.White,
            focusedTextColor = BrandDark,
            unfocusedTextColor = BrandDark,
            focusedBorderColor = BrandOrange,
            unfocusedBorderColor = BrandBorder
        ),
        modifier = Modifier.fillMaxWidth()
    )

    Spacer(modifier = Modifier.height(10.dp))

    // Category Filter Chips (Clean Typography without Emojis)
    val categories = listOf(
        "all" to "All Items",
        "portion" to "Portion Meals",
        "piece" to "Per Piece",
        "weight" to "By Weight / Vol"
    )

    LazyRow(
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        items(categories) { (catKey, catLabel) ->
            val isSelected = selectedCategory == catKey
            Surface(
                shape = RoundedCornerShape(20.dp),
                color = if (isSelected) BrandDark else Color.White,
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
