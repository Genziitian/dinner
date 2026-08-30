package com.dinepos.app.presentation.manager

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
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
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.Order

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
    viewModel: DashboardViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val stats = uiState.stats

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
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
                        Text(
                            text = uiState.restaurantName.ifBlank { "DinePOS" },
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        Text(
                            text = "Manager Dashboard",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextSecondary
                        )
                    }
                },
                actions = {
                    IconButton(onClick = onNavigateToScanner) {
                        Icon(Icons.Default.QrCode, contentDescription = "Scan QR", tint = BrandOrange)
                    }
                    IconButton(onClick = { viewModel.loadDashboard() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = BrandDark)
                    }
                    IconButton(onClick = { viewModel.logout(onLogout) }) {
                        Icon(Icons.AutoMirrored.Filled.Logout, contentDescription = "Logout", tint = StatusError)
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
                .padding(16.dp)
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
                                    text = "TODAY'S TOTAL REVENUE",
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
                                        Text(text = "Cash Collection", color = TextMuted, fontSize = 11.sp)
                                        Text(
                                            text = CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0),
                                            color = BrandEmerald,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 14.sp
                                        )
                                    }
                                    Column(horizontalAlignment = Alignment.End) {
                                        Text(text = "Online / UPI", color = TextMuted, fontSize = 11.sp)
                                        Text(
                                            text = CurrencyFormatter.formatInr(stats?.onlineSales ?: 0.0),
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
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(12.dp)
                            ) {
                                ActionCard(
                                    title = "POS Billing",
                                    subtitle = "Take orders & print",
                                    icon = Icons.Default.ShoppingCart,
                                    iconColor = BrandOrange,
                                    badgeText = "${stats?.totalOrders ?: 0} orders",
                                    onClick = onNavigateToBilling,
                                    modifier = Modifier.weight(1f)
                                )
                                ActionCard(
                                    title = "Order History",
                                    subtitle = "View & search all",
                                    icon = Icons.Default.ReceiptLong,
                                    iconColor = Color(0xFF3B82F6),
                                    onClick = onNavigateToOrders,
                                    modifier = Modifier.weight(1f)
                                )
                            }
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(12.dp)
                            ) {
                                ActionCard(
                                    title = "Menu Items",
                                    subtitle = "Rates & portions",
                                    icon = Icons.Default.RestaurantMenu,
                                    iconColor = BrandEmerald,
                                    onClick = onNavigateToItems,
                                    modifier = Modifier.weight(1f)
                                )
                                ActionCard(
                                    title = "Sales Reports",
                                    subtitle = "Analytics & exports",
                                    icon = Icons.Default.BarChart,
                                    iconColor = Color(0xFF8B5CF6),
                                    onClick = onNavigateToReports,
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
                                            text = "${uiState.recentOrders.size}",
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

                            if (uiState.recentOrders.isEmpty()) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxSize()
                                        .padding(32.dp),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Text("🧾", fontSize = 36.sp)
                                        Spacer(modifier = Modifier.height(8.dp))
                                        Text("No orders placed today yet.", color = TextSecondary, fontSize = 14.sp)
                                    }
                                }
                            } else {
                                LazyColumn(
                                    verticalArrangement = Arrangement.spacedBy(10.dp),
                                    modifier = Modifier.fillMaxSize()
                                ) {
                                    items(uiState.recentOrders) { order ->
                                        RecentOrderCard(order = order, onClick = { onOrderClick(order) })
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
                                text = "TODAY'S REVENUE",
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
                                    text = "Cash: ${CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0)}",
                                    color = BrandEmerald,
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.SemiBold
                                )
                                Text(
                                    text = "UPI: ${CurrencyFormatter.formatInr(stats?.onlineSales ?: 0.0)}",
                                    color = BrandAmber,
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.SemiBold
                                )
                            }
                        }
                    }

                    // Quick Actions Grid
                    Text(
                        text = "QUICK ACTIONS",
                        fontWeight = FontWeight.Bold,
                        fontSize = 12.sp,
                        color = TextSecondary,
                        letterSpacing = 0.5.sp
                    )

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        ActionCard(
                            title = "New Order",
                            subtitle = "POS Billing Screen",
                            icon = Icons.Default.ShoppingCart,
                            iconColor = BrandOrange,
                            onClick = onNavigateToBilling,
                            modifier = Modifier.weight(1f)
                        )
                        ActionCard(
                            title = "Orders",
                            subtitle = "View History",
                            icon = Icons.Default.ReceiptLong,
                            iconColor = Color(0xFF3B82F6),
                            onClick = onNavigateToOrders,
                            modifier = Modifier.weight(1f)
                        )
                    }

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        ActionCard(
                            title = "Menu Items",
                            subtitle = "Rates & Portions",
                            icon = Icons.Default.RestaurantMenu,
                            iconColor = BrandEmerald,
                            onClick = onNavigateToItems,
                            modifier = Modifier.weight(1f)
                        )
                        ActionCard(
                            title = "Reports",
                            subtitle = "Sales & Analytics",
                            icon = Icons.Default.BarChart,
                            iconColor = Color(0xFF8B5CF6),
                            onClick = onNavigateToReports,
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
                            text = "RECENT ORDERS",
                            fontWeight = FontWeight.Bold,
                            fontSize = 12.sp,
                            color = TextSecondary,
                            letterSpacing = 0.5.sp
                        )
                        TextButton(onClick = onNavigateToOrders) {
                            Text("View All", fontWeight = FontWeight.Bold, color = BrandOrange)
                        }
                    }

                    if (uiState.recentOrders.isEmpty()) {
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
                        uiState.recentOrders.forEach { order ->
                            RecentOrderCard(order = order, onClick = { onOrderClick(order) })
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun ActionCard(
    title: String,
    subtitle: String,
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

            Spacer(modifier = Modifier.height(12.dp))
            Text(text = title, fontWeight = FontWeight.Bold, fontSize = 15.sp, color = BrandDark)
            Text(text = subtitle, fontSize = 11.sp, color = TextSecondary)
        }
    }
}

@Composable
private fun RecentOrderCard(
    order: Order,
    onClick: () -> Unit
) {
    Card(
        onClick = onClick,
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = BrandSurface),
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
            Column(modifier = Modifier.weight(1f)) {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "Order #${order.orderNumber}",
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 15.sp,
                        color = BrandDark
                    )
                    Surface(
                        shape = RoundedCornerShape(6.dp),
                        color = if (order.paymentMethod.lowercase() == "cash") BrandEmerald.copy(alpha = 0.12f) else BrandAmber.copy(alpha = 0.12f)
                    ) {
                        Text(
                            text = order.paymentMethod.uppercase(),
                            color = if (order.paymentMethod.lowercase() == "cash") BrandEmerald else BrandAmber,
                            fontWeight = FontWeight.Bold,
                            fontSize = 10.sp,
                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                        )
                    }
                }
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = "${order.orderTime} · ${order.customerName?.ifBlank { "Walk-in Customer" } ?: "Walk-in Customer"}",
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
