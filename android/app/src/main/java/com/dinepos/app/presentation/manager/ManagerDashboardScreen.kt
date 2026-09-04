package com.dinepos.app.presentation.manager

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.combinedClickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.filled.Logout
import androidx.compose.material.icons.automirrored.filled.ReceiptLong
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.Order
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ManagerDashboardScreen(
    onNavigateToBilling: () -> Unit,
    onNavigateToOrders: () -> Unit,
    onNavigateToItems: () -> Unit,
    onNavigateToReports: () -> Unit,
    onNavigateToScanner: () -> Unit = {},
    onNavigateBack: (() -> Unit)? = null,
    onOrderClick: (Order) -> Unit,
    onLogout: () -> Unit,
    onNavigateToMillOrder: () -> Unit = {},
    onNavigateToMillOrders: () -> Unit = {},
    onNavigateToMillServices: () -> Unit = {},
    onNavigateToMillCustomers: () -> Unit = {},
    viewModel: DashboardViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val stats = uiState.stats
    val sessionManager = com.dinepos.app.DinePosApp.instance.sessionManager
    val isMill = uiState.isMill || sessionManager.isMill()
    val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"

    val deletedOrderIds by sessionManager.deletedOrderIds.collectAsState()
    val hiddenOrderIds by sessionManager.hiddenOrderIds.collectAsState()

    val visibleRecentOrders: List<Order> = remember(uiState.recentOrders, deletedOrderIds, hiddenOrderIds) {
        uiState.recentOrders.filter { order ->
            !deletedOrderIds.contains(order.id.toString()) &&
            !hiddenOrderIds.contains(order.id.toString()) &&
            !order.status.equals("cancelled", ignoreCase = true)
        }
    }

    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    var selectedOrderForAction by remember { mutableStateOf<Order?>(null) }
    var deleteConfirmOrder by remember { mutableStateOf<Order?>(null) }

    val lifecycleOwner = androidx.lifecycle.compose.LocalLifecycleOwner.current
    DisposableEffect(lifecycleOwner) {
        val observer = androidx.lifecycle.LifecycleEventObserver { _, event ->
            if (event == androidx.lifecycle.Lifecycle.Event.ON_RESUME) {
                viewModel.loadDashboard()
            }
        }
        lifecycleOwner.lifecycle.addObserver(observer)
        onDispose {
            lifecycleOwner.lifecycle.removeObserver(observer)
        }
    }

    Scaffold(
        contentWindowInsets = WindowInsets(0.dp),
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                windowInsets = WindowInsets.statusBars,
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
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(8.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Text(
                                text = uiState.restaurantName.ifBlank { if (isMill) "Atta Mill" else "Restaurant" },
                                style = MaterialTheme.typography.titleLarge,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark,
                                maxLines = 1,
                                overflow = TextOverflow.Ellipsis,
                                modifier = Modifier.weight(1f, fill = false)
                            )
                            Surface(
                                shape = RoundedCornerShape(6.dp),
                                color = BrandOrange.copy(alpha = 0.12f),
                                border = BorderStroke(1.dp, BrandOrange.copy(alpha = 0.35f))
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
                                        text = if (isMill) "MILL" else "GI ORDER POS",
                                        fontSize = 9.sp,
                                        fontWeight = FontWeight.Black,
                                        color = BrandOrange,
                                        letterSpacing = 0.6.sp
                                    )
                                }
                            }
                        }
                        Text(
                            text = if (isMill) "Mill Dashboard" else "Manager Dashboard",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextSecondary
                        )
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
            )
        }
    ) { paddingValues ->
        BoxWithConstraints(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(horizontal = 16.dp, vertical = 8.dp)
        ) {
            val isTablet = maxWidth >= 680.dp

            if (uiState.isLoading) {
                CircularProgressIndicator(color = BrandOrange, modifier = Modifier.align(Alignment.Center))
            } else if (isTablet) {
                // ==========================================
                // TABLET / LARGE SCREEN RESPONSIVE DUAL-PANE
                // ==========================================
                Row(
                    modifier = Modifier.fillMaxSize(),
                    horizontalArrangement = Arrangement.spacedBy(18.dp)
                ) {
                    // LEFT PANE: Metrics & Quick Actions
                    Column(
                        modifier = Modifier
                            .weight(0.45f)
                            .fillMaxHeight()
                            .verticalScroll(rememberScrollState()),
                        verticalArrangement = Arrangement.spacedBy(16.dp)
                    ) {
                        // Revenue Summary Banner Card
                        Card(
                            shape = RoundedCornerShape(18.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandDark),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Column(modifier = Modifier.padding(20.dp)) {
                                Text(
                                    text = if (isMill) "TODAY'S MILL REVENUE" else "TODAY'S TOTAL REVENUE",
                                    color = TextMuted,
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.ExtraBold,
                                    letterSpacing = 1.sp
                                )
                                Spacer(modifier = Modifier.height(6.dp))
                                Text(
                                    text = CurrencyFormatter.formatInr(stats?.totalSales ?: 0.0),
                                    color = Color.White,
                                    fontSize = 32.sp,
                                    fontWeight = FontWeight.Black
                                )
                                Spacer(modifier = Modifier.height(14.dp))
                                HorizontalDivider(color = Color.White.copy(alpha = 0.1f), thickness = 1.dp)
                                Spacer(modifier = Modifier.height(12.dp))
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween
                                ) {
                                    Column {
                                        Text(text = if (isMill) "Paid Collection" else "Cash Collection", color = TextMuted, fontSize = 11.sp)
                                        Text(
                                            text = CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0),
                                            color = BrandEmerald,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 14.sp
                                        )
                                    }
                                    Column(horizontalAlignment = Alignment.End) {
                                        Text(text = if (isMill) "Pending Due" else "Online / UPI", color = TextMuted, fontSize = 11.sp)
                                        Text(
                                            text = CurrencyFormatter.formatInr(if (isMill) (stats?.unpaidAmount ?: 0.0) else (stats?.onlineSales ?: 0.0)),
                                            color = BrandAmber,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 14.sp
                                        )
                                    }
                                }
                            }
                        }

                        // Quick Actions 2x2 Grid
                        Text(
                            text = "QUICK ACTIONS",
                            fontWeight = FontWeight.Bold,
                            fontSize = 12.sp,
                            color = TextSecondary,
                            letterSpacing = 0.5.sp
                        )

                        Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                            if (!isMill) {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                                ) {
                                    ActionCard(
                                        title = "POS Billing",
                                        icon = Icons.Default.ShoppingCart,
                                        iconColor = BrandOrange,
                                        badgeText = "${stats?.totalOrders ?: 0} orders",
                                        onClick = onNavigateToBilling,
                                        modifier = Modifier.weight(1f)
                                    )
                                    ActionCard(
                                        title = "Order History",
                                        icon = Icons.AutoMirrored.Filled.ReceiptLong,
                                        iconColor = Color(0xFF3B82F6),
                                        onClick = onNavigateToOrders,
                                        modifier = Modifier.weight(1f)
                                    )
                                }
                            }
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(12.dp)
                            ) {
                                ActionCard(
                                    title = if (isMill) "Grinding Rates" else "Menu Items",
                                    icon = if (isMill) Icons.Default.Storefront else Icons.Default.RestaurantMenu,
                                    iconColor = BrandEmerald,
                                    onClick = if (isMill) onNavigateToMillServices else onNavigateToItems,
                                    modifier = Modifier.weight(1f)
                                )
                                ActionCard(
                                    title = if (isMill) "Customers" else "Sales Reports",
                                    icon = if (isMill) Icons.Default.People else Icons.Default.BarChart,
                                    iconColor = Color(0xFF8B5CF6),
                                    onClick = if (isMill) onNavigateToMillCustomers else onNavigateToReports,
                                    modifier = Modifier.weight(1f)
                                )
                            }
                        }
                    }

                    // RIGHT PANE: Recent Orders Full Height Panel
                    Surface(
                        shape = RoundedCornerShape(18.dp),
                        color = BrandSurface,
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier
                            .weight(0.55f)
                            .fillMaxHeight()
                    ) {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .padding(18.dp)
                        ) {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Row(
                                    verticalAlignment = Alignment.CenterVertically,
                                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                                ) {
                                    Text(
                                        text = "Recent Orders",
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 16.sp,
                                        color = BrandDark
                                    )
                                    Surface(
                                        shape = CircleShape,
                                        color = BrandOrange.copy(alpha = 0.12f)
                                    ) {
                                        Text(
                                            text = "${visibleRecentOrders.size}",
                                            color = BrandOrange,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 12.sp,
                                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                                        )
                                    }
                                }

                                TextButton(onClick = onNavigateToOrders) {
                                    Text("View All", fontWeight = FontWeight.Bold, color = BrandOrange)
                                }
                            }

                            Spacer(modifier = Modifier.height(10.dp))
                            HorizontalDivider(color = BrandBorder, thickness = 1.dp)
                            Spacer(modifier = Modifier.height(10.dp))

                            if (visibleRecentOrders.isEmpty()) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxSize()
                                        .padding(32.dp),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Icon(
                                            imageVector = Icons.AutoMirrored.Filled.ReceiptLong,
                                            contentDescription = null,
                                            tint = TextMuted,
                                            modifier = Modifier.size(36.dp)
                                        )
                                        Spacer(modifier = Modifier.height(8.dp))
                                        Text("No orders placed today yet.", color = TextSecondary, fontSize = 14.sp)
                                    }
                                }
                            } else {
                                LazyColumn(
                                    verticalArrangement = Arrangement.spacedBy(10.dp),
                                    modifier = Modifier.fillMaxSize()
                                ) {
                                    items(visibleRecentOrders) { order ->
                                        RecentOrderCard(
                                            order = order,
                                            onClick = { onOrderClick(order) },
                                            onLongClick = { selectedOrderForAction = order }
                                        )
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                // ==========================================
                // PHONE / COMPACT SCREEN SINGLE COLUMN
                // ==========================================
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState()),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Revenue Card
                    Card(
                        shape = RoundedCornerShape(18.dp),
                        colors = CardDefaults.cardColors(containerColor = BrandDark),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(18.dp)) {
                            Text(
                                text = if (isMill) com.dinepos.app.core.localization.L10n.todayMillRevenue(isHi) else com.dinepos.app.core.localization.L10n.todayRevenue(isHi),
                                color = TextMuted,
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                letterSpacing = 1.sp
                            )
                            Spacer(modifier = Modifier.height(4.dp))
                            Text(
                                text = CurrencyFormatter.formatInr(stats?.totalSales ?: 0.0),
                                color = Color.White,
                                fontSize = 28.sp,
                                fontWeight = FontWeight.Black
                            )
                            Spacer(modifier = Modifier.height(12.dp))
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Text(
                                    text = if (isMill) "${com.dinepos.app.core.localization.L10n.paid(isHi)}: ${CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0)}" else "Cash: ${CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0)}",
                                    color = BrandEmerald,
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.SemiBold
                                )
                                Text(
                                    text = if (isMill) "${com.dinepos.app.core.localization.L10n.pending(isHi)}: ${CurrencyFormatter.formatInr(stats?.unpaidAmount ?: 0.0)}" else "UPI: ${CurrencyFormatter.formatInr(stats?.onlineSales ?: 0.0)}",
                                    color = BrandAmber,
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.SemiBold
                                )
                            }
                        }
                    }

                    // Quick Actions Grid
                    Text(
                        text = com.dinepos.app.core.localization.L10n.quickActions(isHi),
                        fontWeight = FontWeight.Bold,
                        fontSize = 12.sp,
                        color = TextSecondary,
                        letterSpacing = 0.5.sp
                    )

                    if (!isMill) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            ActionCard(
                                title = "New Order",
                                icon = Icons.Default.ShoppingCart,
                                iconColor = BrandOrange,
                                onClick = onNavigateToBilling,
                                modifier = Modifier.weight(1f)
                            )
                            ActionCard(
                                title = "Orders",
                                icon = Icons.AutoMirrored.Filled.ReceiptLong,
                                iconColor = Color(0xFF3B82F6),
                                onClick = onNavigateToOrders,
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        ActionCard(
                            title = if (isMill) com.dinepos.app.core.localization.L10n.grindingRates(isHi) else "Menu Items",
                            icon = if (isMill) Icons.Default.Storefront else Icons.Default.RestaurantMenu,
                            iconColor = BrandEmerald,
                            onClick = if (isMill) onNavigateToMillServices else onNavigateToItems,
                            modifier = Modifier.weight(1f)
                        )
                        ActionCard(
                            title = if (isMill) com.dinepos.app.core.localization.L10n.customers(isHi) else "Reports",
                            icon = if (isMill) Icons.Default.People else Icons.Default.BarChart,
                            iconColor = Color(0xFF8B5CF6),
                            onClick = if (isMill) onNavigateToMillCustomers else onNavigateToReports,
                            modifier = Modifier.weight(1f)
                        )
                    }

                    // Recent Orders
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = com.dinepos.app.core.localization.L10n.recentOrders(isHi),
                            fontWeight = FontWeight.Bold,
                            fontSize = 12.sp,
                            color = TextSecondary,
                            letterSpacing = 0.5.sp
                        )
                        TextButton(onClick = onNavigateToOrders) {
                            Text(com.dinepos.app.core.localization.L10n.viewAll(isHi), fontWeight = FontWeight.Bold, color = BrandOrange)
                        }
                    }

                    if (visibleRecentOrders.isEmpty()) {
                        Card(
                            shape = RoundedCornerShape(14.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandSurface),
                            border = BorderStroke(1.dp, BrandBorder),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(28.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                Text("No recent orders recorded today.", color = TextSecondary, fontSize = 13.sp)
                            }
                        }
                    } else {
                        visibleRecentOrders.forEach { order ->
                            RecentOrderCard(
                                order = order,
                                onClick = { onOrderClick(order) },
                                onLongClick = { selectedOrderForAction = order }
                            )
                        }
                    }
                    Spacer(modifier = Modifier.height(16.dp))
                }
            }
        }
    }

    // Hold Action Sheet for Recent Orders (Hide / Unhide / Delete)
    if (selectedOrderForAction != null) {
        val selOrder = selectedOrderForAction!!
        val isOrderCurrentlyHidden = sessionManager.isOrderHidden(selOrder.id)

        ModalBottomSheet(
            onDismissRequest = { selectedOrderForAction = null },
            containerColor = BrandSurface,
            shape = RoundedCornerShape(topStart = 20.dp, topEnd = 20.dp)
        ) {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
                    .padding(bottom = 32.dp),
                verticalArrangement = Arrangement.spacedBy(14.dp)
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Column {
                        Text(
                            text = "Order #${selOrder.orderNumber}",
                            fontWeight = FontWeight.ExtraBold,
                            fontSize = 18.sp,
                            color = BrandDark
                        )
                        Text(
                            text = "${selOrder.customerName ?: if (isHi) "ग्राहक" else "Customer"} • ${CurrencyFormatter.formatInr(selOrder.total)}",
                            fontSize = 13.sp,
                            color = TextSecondary
                        )
                    }
                    IconButton(onClick = { selectedOrderForAction = null }) {
                        Icon(Icons.Default.Close, contentDescription = "Close", tint = TextMuted)
                    }
                }

                HorizontalDivider(color = BrandBorder)

                // Hide / Unhide option
                Card(
                    onClick = {
                        val isNowHidden = sessionManager.toggleOrderHidden(selOrder.id)
                        selectedOrderForAction = null
                        Toast.makeText(
                            context,
                            if (isNowHidden) {
                                if (isHi) "Order #${selOrder.orderNumber} छिपा दिया गया (Hidden)" else "Order #${selOrder.orderNumber} hidden"
                            } else {
                                if (isHi) "Order #${selOrder.orderNumber} वापस दिखाया गया" else "Order #${selOrder.orderNumber} unhidden"
                            },
                            Toast.LENGTH_SHORT
                        ).show()
                    },
                    colors = CardDefaults.cardColors(containerColor = BrandBackground),
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(
                        modifier = Modifier.padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        Icon(
                            imageVector = if (isOrderCurrentlyHidden) Icons.Default.Visibility else Icons.Default.VisibilityOff,
                            contentDescription = null,
                            tint = BrandDark
                        )
                        Column {
                            Text(
                                text = if (isOrderCurrentlyHidden) {
                                    if (isHi) "ऑर्डर वापस दिखाएं (Unhide Order)" else "Unhide Order"
                                } else {
                                    if (isHi) "ऑर्डर छिपाएं (Hide Order)" else "Hide Order"
                                },
                                fontWeight = FontWeight.Bold,
                                fontSize = 14.sp,
                                color = BrandDark
                            )
                            Text(
                                text = if (isOrderCurrentlyHidden) {
                                    if (isHi) "सक्रिय ऑर्डर्स और कुल योग में जोड़ें" else "Restore to active orders and totals"
                                } else {
                                    if (isHi) "कुल योग और ऑर्डर्स में न जोड़ें" else "Do not count in totals or any orders"
                                },
                                fontSize = 11.sp,
                                color = TextSecondary
                            )
                        }
                    }
                }

                // Delete option
                Card(
                    onClick = {
                        deleteConfirmOrder = selOrder
                        selectedOrderForAction = null
                    },
                    colors = CardDefaults.cardColors(containerColor = StatusError.copy(alpha = 0.08f)),
                    border = BorderStroke(1.dp, StatusError.copy(alpha = 0.3f)),
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(
                        modifier = Modifier.padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Delete,
                            contentDescription = null,
                            tint = StatusError
                        )
                        Column {
                            Text(
                                text = if (isHi) "ऑर्डर हटाएं (Delete Order)" else "Delete Order",
                                fontWeight = FontWeight.Bold,
                                fontSize = 14.sp,
                                color = StatusError
                            )
                            Text(
                                text = if (isHi) "इस ऑर्डर को हमेशा के लिए हटाएं" else "Permanently remove this order",
                                fontSize = 11.sp,
                                color = StatusError.copy(alpha = 0.8f)
                            )
                        }
                    }
                }
            }
        }
    }

    // Delete Confirmation Dialog
    deleteConfirmOrder?.let { toDelete ->
        AlertDialog(
            onDismissRequest = { deleteConfirmOrder = null },
            title = {
                Text(
                    text = if (isHi) "ऑर्डर हटाएं?" else "Delete Order?",
                    fontWeight = FontWeight.Bold,
                    color = BrandDark
                )
            },
            text = {
                Text(
                    text = if (isHi) {
                        "क्या आप वाकई Order #${toDelete.orderNumber} को हटाना चाहते हैं? यह पूरी तरह हटा दिया जाएगा।"
                    } else {
                        "Are you sure you want to delete Order #${toDelete.orderNumber}? This will permanently remove the order."
                    },
                    fontSize = 14.sp
                )
            },
            confirmButton = {
                Button(
                    onClick = {
                        sessionManager.markOrderDeleted(toDelete.id)
                        scope.launch {
                            if (isMill) {
                                com.dinepos.app.DinePosApp.instance.millRepository.deleteOrder(toDelete.id)
                            }
                            viewModel.loadDashboard()
                        }
                        deleteConfirmOrder = null
                        Toast.makeText(
                            context,
                            if (isHi) "Order #${toDelete.orderNumber} हटा दिया गया" else "Order #${toDelete.orderNumber} deleted",
                            Toast.LENGTH_SHORT
                        ).show()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = StatusError),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Text(if (isHi) "हटाएं (Delete)" else "Delete", color = Color.White, fontWeight = FontWeight.Bold)
                }
            },
            dismissButton = {
                TextButton(onClick = { deleteConfirmOrder = null }) {
                    Text(if (isHi) "रद्द करें" else "Cancel", fontWeight = FontWeight.Bold, color = BrandDark)
                }
            }
        )
    }
}

@Composable
private fun ActionCard(
    title: String,
    subtitle: String? = null,
    icon: ImageVector,
    iconColor: Color,
    badgeText: String? = null,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        onClick = onClick,
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = BrandSurface),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = modifier
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(14.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.Top
            ) {
                Surface(
                    shape = RoundedCornerShape(12.dp),
                    color = iconColor.copy(alpha = 0.12f),
                    modifier = Modifier.size(42.dp)
                ) {
                    Box(contentAlignment = Alignment.Center) {
                        Icon(icon, contentDescription = title, tint = iconColor, modifier = Modifier.size(24.dp))
                    }
                }

                if (!badgeText.isNullOrBlank()) {
                    Surface(
                        shape = CircleShape,
                        color = iconColor.copy(alpha = 0.15f)
                    ) {
                        Text(
                            text = badgeText,
                            color = iconColor,
                            fontWeight = FontWeight.Bold,
                            fontSize = 11.sp,
                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(10.dp))
            Text(text = title, fontWeight = FontWeight.Bold, fontSize = 15.sp, color = BrandDark)
            if (!subtitle.isNullOrBlank()) {
                Text(text = subtitle, fontSize = 11.sp, color = TextSecondary)
            }
        }
    }
}

@OptIn(ExperimentalFoundationApi::class)
@Composable
private fun RecentOrderCard(
    order: Order,
    onClick: () -> Unit,
    onLongClick: (() -> Unit)? = null
) {
    Card(
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = BrandSurface),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = Modifier
            .fillMaxWidth()
            .combinedClickable(
                onClick = onClick,
                onLongClick = onLongClick
            )
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(14.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = if (isHi) "ऑर्डर #${order.orderNumber}" else "Order #${order.orderNumber}",
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 15.sp,
                        color = BrandDark
                    )
                    val isDelivered = order.status.equals("delivered", ignoreCase = true)
                    val isUnpaid = order.paymentMethod.lowercase().contains("unpaid") && !isDelivered
                    Surface(
                        shape = RoundedCornerShape(6.dp),
                        color = if (isUnpaid) BrandAmber.copy(alpha = 0.12f) else BrandEmerald.copy(alpha = 0.12f)
                    ) {
                        Text(
                            text = if (isUnpaid) com.dinepos.app.core.localization.L10n.unpaid(isHi).uppercase() else com.dinepos.app.core.localization.L10n.paidStatus(isHi),
                            color = if (isUnpaid) BrandAmber else BrandEmerald,
                            fontWeight = FontWeight.Bold,
                            fontSize = 10.sp,
                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                        )
                    }
                }
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = "${order.orderTime} · ${order.customerName?.ifBlank { com.dinepos.app.core.localization.L10n.walkInCustomer(isHi) } ?: com.dinepos.app.core.localization.L10n.walkInCustomer(isHi)}",
                    fontSize = 12.sp,
                    color = TextSecondary
                )
            }

            Text(
                text = CurrencyFormatter.formatInr(order.total),
                fontWeight = FontWeight.ExtraBold,
                fontSize = 16.sp,
                color = BrandDark
            )
        }
    }
}
