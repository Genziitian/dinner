package com.dinepos.app.presentation.summary

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SummaryScreen(
    onNavigateBack: () -> Unit,
    viewModel: SummaryViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val stats = uiState.stats

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(text = "Today's Summary", fontWeight = FontWeight.Bold)
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    IconButton(onClick = { viewModel.loadSummary() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh")
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
                .padding(16.dp),
            contentAlignment = Alignment.TopCenter
        ) {
            val isTablet = maxWidth >= 600.dp

            if (uiState.isLoading) {
                CircularProgressIndicator(color = BrandOrange, modifier = Modifier.align(Alignment.Center))
            } else if (stats != null) {
                Column(
                    modifier = Modifier
                        .widthIn(max = 680.dp)
                        .fillMaxWidth()
                        .verticalScroll(rememberScrollState()),
                    verticalArrangement = Arrangement.spacedBy(14.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    // Total Sales Hero Card
                    Card(
                        shape = RoundedCornerShape(20.dp),
                        colors = CardDefaults.cardColors(containerColor = BrandDark),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(24.dp)) {
                            Text(
                                text = "TODAY'S TOTAL COLLECTION",
                                color = TextMuted,
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(6.dp))
                            Text(
                                text = CurrencyFormatter.formatInr(stats.totalSales),
                                color = Color.White,
                                fontSize = 32.sp,
                                fontWeight = FontWeight.Black
                            )
                            Spacer(modifier = Modifier.height(10.dp))
                            Text(
                                text = "${stats.totalOrders} total orders processed today",
                                color = BrandAmber,
                                fontWeight = FontWeight.SemiBold,
                                fontSize = 14.sp
                            )
                        }
                    }

                    if (isTablet) {
                        // Side-by-Side Breakdown on Tablets
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            // Cash Collection Card
                            Card(
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                                border = BorderStroke(1.5.dp, BrandEmerald),
                                modifier = Modifier.weight(1f)
                            ) {
                                Column(modifier = Modifier.padding(18.dp)) {
                                    Text(text = "💵 Cash Sales", fontWeight = FontWeight.Bold, color = BrandDark)
                                    Text(text = "In-drawer collected", fontSize = 12.sp, color = TextSecondary)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text(
                                        text = CurrencyFormatter.formatInr(stats.cashSales),
                                        fontSize = 22.sp,
                                        fontWeight = FontWeight.ExtraBold,
                                        color = BrandEmerald
                                    )
                                }
                            }

                            // Online / UPI Card
                            Card(
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                                border = BorderStroke(1.5.dp, BrandOrange),
                                modifier = Modifier.weight(1f)
                            ) {
                                Column(modifier = Modifier.padding(18.dp)) {
                                    Text(text = "📱 Online / UPI", fontWeight = FontWeight.Bold, color = BrandDark)
                                    Text(text = "Direct digital transfers", fontSize = 12.sp, color = TextSecondary)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text(
                                        text = CurrencyFormatter.formatInr(stats.onlineSales),
                                        fontSize = 22.sp,
                                        fontWeight = FontWeight.ExtraBold,
                                        color = BrandOrangeDark
                                    )
                                }
                            }
                        }
                    } else {
                        // Stacked Breakdown on Phones
                        Card(
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandSurface),
                            border = BorderStroke(1.5.dp, BrandEmerald),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(18.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column {
                                    Text(text = "💵 Cash Sales", fontWeight = FontWeight.Bold, color = BrandDark)
                                    Text(text = "In-drawer collected", fontSize = 12.sp, color = TextSecondary)
                                }
                                Text(
                                    text = CurrencyFormatter.formatInr(stats.cashSales),
                                    fontSize = 20.sp,
                                    fontWeight = FontWeight.ExtraBold,
                                    color = BrandEmerald
                                )
                            }
                        }

                        Card(
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandSurface),
                            border = BorderStroke(1.5.dp, BrandOrange),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(18.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column {
                                    Text(text = "📱 Online / UPI", fontWeight = FontWeight.Bold, color = BrandDark)
                                    Text(text = "Direct digital transfers", fontSize = 12.sp, color = TextSecondary)
                                }
                                Text(
                                    text = CurrencyFormatter.formatInr(stats.onlineSales),
                                    fontSize = 20.sp,
                                    fontWeight = FontWeight.ExtraBold,
                                    color = BrandOrangeDark
                                )
                            }
                        }
                    }

                    // Average Order Value Card
                    Card(
                        shape = RoundedCornerShape(16.dp),
                        colors = CardDefaults.cardColors(containerColor = BrandSurface),
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(18.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column {
                                Text(text = "📊 Average Ticket", fontWeight = FontWeight.Bold, color = BrandDark)
                                Text(text = "Per-order ticket size", fontSize = 12.sp, color = TextSecondary)
                            }
                            Text(
                                text = CurrencyFormatter.formatInr(stats.avgOrderValue),
                                fontSize = 18.sp,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                        }
                    }
                }
            } else {
                Text(
                    text = uiState.errorMessage ?: "No summary data found.",
                    color = TextSecondary,
                    modifier = Modifier.align(Alignment.Center)
                )
            }
        }
    }
}
