package com.dinepos.app.presentation.manager

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
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
                        Icon(Icons.Default.Logout, contentDescription = "Logout", tint = StatusError)
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
            val isTablet = maxWidth >= 600.dp

            if (uiState.isLoading) {
                CircularProgressIndicator(color = BrandOrange, modifier = Modifier.align(Alignment.Center))
            } else {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState()),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Metrics Row
                    if (isTablet) {
                        // 3 Balanced Metric Cards Row for Tablets
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            Card(
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = BrandDark),
                                modifier = Modifier.weight(1f)
                            ) {
                                Column(modifier = Modifier.padding(18.dp)) {
                                    Text(text = "TOTAL SALES TODAY", color = TextMuted, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = CurrencyFormatter.formatInr(stats?.totalSales ?: 0.0),
                                        color = Color.White,
                                        fontSize = 24.sp,
                                        fontWeight = FontWeight.Black
                                    )
                                    Spacer(modifier = Modifier.height(6.dp))
                                    Text(text = "${stats?.totalOrders ?: 0} orders completed", color = TextMuted, fontSize = 12.sp)
                                }
                            }

                            Card(
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = BrandEmeraldLight),
                                border = BorderStroke(1.dp, BrandEmerald.copy(alpha = 0.3f)),
                                modifier = Modifier.weight(1f)
                            ) {
                                Column(modifier = Modifier.padding(18.dp)) {
                                    Text(text = "CASH COLLECTION", color = BrandEmerald, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0),
                                        color = BrandEmerald,
                                        fontSize = 24.sp,
                                        fontWeight = FontWeight.Black
                                    )
                                    Spacer(modifier = Modifier.height(6.dp))
                                    Text(text = "Physical cash receipts", color = TextSecondary, fontSize = 12.sp)
                                }
                            }

                            Card(
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = BrandOrangeLight),
                                border = BorderStroke(1.dp, BrandOrange.copy(alpha = 0.3f)),
                                modifier = Modifier.weight(1f)
                            ) {
                                Column(modifier = Modifier.padding(18.dp)) {
                                    Text(text = "ONLINE / UPI SALES", color = BrandOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = CurrencyFormatter.formatInr(stats?.onlineSales ?: 0.0),
                                        color = BrandOrange,
                                        fontSize = 24.sp,
                                        fontWeight = FontWeight.Black
                                    )
                                    Spacer(modifier = Modifier.height(6.dp))
                                    Text(text = "QR code & digital payments", color = TextSecondary, fontSize = 12.sp)
                                }
                            }
                        }
                    } else {
                        // Phone Hero Card: Stacked Revenue
                        Card(
                            shape = RoundedCornerShape(20.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandDark),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Column(modifier = Modifier.padding(22.dp)) {
                                Text(text = "TODAY'S REVENUE", color = TextMuted, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                                Spacer(modifier = Modifier.height(6.dp))
                                Text(
                                    text = CurrencyFormatter.formatInr(stats?.totalSales ?: 0.0),
                                    color = Color.White,
                                    fontSize = 32.sp,
                                    fontWeight = FontWeight.Black
                                )
                                Spacer(modifier = Modifier.height(10.dp))
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween
                                ) {
                                    Text(
                                        text = "Cash: ${CurrencyFormatter.formatInr(stats?.cashSales ?: 0.0)}",
                                        color = BrandEmerald,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 13.sp
                                    )
                                    Text(
                                        text = "UPI: ${CurrencyFormatter.formatInr(stats?.onlineSales ?: 0.0)}",
                                        color = BrandAmber,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 13.sp
                                    )
                                }
                            }
                        }
                    }

                    // Quick Management Actions Grid
                    Text(text = "QUICK ACTIONS", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = TextSecondary)

                    if (isTablet) {
                        // 4 Cards in 1 Row on Tablet
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            ActionCard(
                                title = "New Order",
                                subtitle = "POS Billing Screen",
                                icon = Icons.Default.AddShoppingCart,
                                color = BrandOrange,
                                onClick = onNavigateToBilling,
                                modifier = Modifier.weight(1f)
                            )
                            ActionCard(
                                title = "Orders",
                                subtitle = "View History",
                                icon = Icons.Default.ReceiptLong,
                                color = BrandDark,
                                onClick = onNavigateToOrders,
                                modifier = Modifier.weight(1f)
                            )
                            ActionCard(
                                title = "Menu Items",
                                subtitle = "Rates & Portions",
                                icon = Icons.Default.RestaurantMenu,
                                color = BrandEmerald,
                                onClick = onNavigateToItems,
                                modifier = Modifier.weight(1f)
                            )
                            ActionCard(
                                title = "Reports",
                                subtitle = "Sales & Analytics",
                                icon = Icons.Default.BarChart,
                                color = Color(0xFF0284C7),
                                onClick = onNavigateToReports,
                                modifier = Modifier.weight(1f)
                            )
                        }
                    } else {
                        // 2 Rows of 2 on Phone
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            ActionCard(
                                title = "New Order",
                                subtitle = "POS Billing Screen",
                                icon = Icons.Default.AddShoppingCart,
                                color = BrandOrange,
                                onClick = onNavigateToBilling,
                                modifier = Modifier.weight(1f)
                            )
                            ActionCard(
                                title = "Orders",
                                subtitle = "View History",
                                icon = Icons.Default.ReceiptLong,
                                color = BrandDark,
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
                                color = BrandEmerald,
                                onClick = onNavigateToItems,
                                modifier = Modifier.weight(1f)
                            )
                            ActionCard(
                                title = "Reports",
                                subtitle = "Sales & Analytics",
                                icon = Icons.Default.BarChart,
                                color = Color(0xFF0284C7),
                                onClick = onNavigateToReports,
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }

                    // Recent Orders Feed
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(text = "RECENT ORDERS", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = TextSecondary)
                        TextButton(onClick = onNavigateToOrders) {
                            Text("View All", fontWeight = FontWeight.Bold, color = BrandOrange)
                        }
                    }

                    if (uiState.recentOrders.isEmpty()) {
                        Surface(
                            shape = RoundedCornerShape(12.dp),
                            color = BrandSurface,
                            border = BorderStroke(1.dp, BrandBorder),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Box(modifier = Modifier.padding(24.dp), contentAlignment = Alignment.Center) {
                                Text("No recent orders recorded today.", color = TextSecondary)
                            }
                        }
                    } else {
                        uiState.recentOrders.take(6).forEach { order ->
                            Card(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable { onOrderClick(order) },
                                shape = RoundedCornerShape(14.dp),
                                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                                border = BorderStroke(1.dp, BrandBorder)
                            ) {
                                Row(
                                    modifier = Modifier.padding(14.dp),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Column {
                                        Text(text = "Order #${order.orderNumber}", fontWeight = FontWeight.Bold, color = BrandDark)
                                        Text(
                                            text = "${order.orderTime} · ${order.customerName ?: "Walk-in"} · ${order.paymentMethod}",
                                            fontSize = 12.sp,
                                            color = TextSecondary
                                        )
                                    }
                                    Text(
                                        text = CurrencyFormatter.formatInr(order.total),
                                        fontWeight = FontWeight.ExtraBold,
                                        color = BrandDark
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun ActionCard(
    title: String,
    subtitle: String,
    icon: ImageVector,
    color: Color,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        onClick = onClick,
        modifier = modifier.height(105.dp),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = BrandSurface),
        border = BorderStroke(1.dp, BrandBorder),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(14.dp),
            verticalArrangement = Arrangement.SpaceBetween
        ) {
            Icon(
                imageVector = icon,
                contentDescription = null,
                tint = color,
                modifier = Modifier.size(28.dp)
            )
            Column {
                Text(
                    text = title,
                    fontWeight = FontWeight.Bold,
                    fontSize = 15.sp,
                    color = BrandDark
                )
                Text(
                    text = subtitle,
                    fontSize = 11.sp,
                    color = TextSecondary
                )
            }
        }
    }
}
