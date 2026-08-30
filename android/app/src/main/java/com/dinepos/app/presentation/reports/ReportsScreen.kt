package com.dinepos.app.presentation.reports

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
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
import com.dinepos.app.domain.model.DailyStats

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReportsScreen(
    onNavigateBack: () -> Unit,
    viewModel: ReportsViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val report = uiState.report

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = { Text("Financial Reports", fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    IconButton(onClick = { viewModel.loadReports() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh")
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
            )
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp)
        ) {
            // Period Filters
            val periods = listOf(
                "today" to "Today",
                "yesterday" to "Yesterday",
                "7days" to "Last 7 Days",
                "this_month" to "This Month",
                "last_month" to "Last Month",
                "6months" to "6 Months",
                "year" to "Yearly"
            )

            LazyRow(
                horizontalArrangement = Arrangement.spacedBy(8.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                items(periods) { (key, label) ->
                    val isSelected = uiState.selectedPeriod == key
                    Surface(
                        shape = RoundedCornerShape(20.dp),
                        color = if (isSelected) BrandDark else BrandSurface,
                        border = BorderStroke(1.dp, if (isSelected) BrandDark else BrandBorder),
                        modifier = Modifier.clickable { viewModel.onPeriodChange(key) }
                    ) {
                        Text(
                            text = label,
                            color = if (isSelected) Color.White else TextSecondary,
                            fontWeight = FontWeight.Bold,
                            fontSize = 13.sp,
                            modifier = Modifier.padding(horizontal = 14.dp, vertical = 8.dp)
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            if (uiState.isLoading) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = BrandOrange)
                }
            } else if (report != null) {
                Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState()),
                    verticalArrangement = Arrangement.spacedBy(14.dp)
                ) {
                    // Main Period Metric Card
                    Card(
                        shape = RoundedCornerShape(20.dp),
                        colors = CardDefaults.cardColors(containerColor = BrandDark),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(22.dp)) {
                            Text(
                                text = "TOTAL SALES (${report.currentPeriod.uppercase()})",
                                color = TextMuted,
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(6.dp))
                            Text(
                                text = CurrencyFormatter.formatInr(report.stats.totalSales),
                                color = Color.White,
                                fontSize = 32.sp,
                                fontWeight = FontWeight.Black
                            )
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                text = "${report.stats.totalOrders} orders completed",
                                color = BrandAmber,
                                fontWeight = FontWeight.SemiBold,
                                fontSize = 14.sp
                            )
                        }
                    }

                    // Payment Breakdown
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        Card(
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandSurface),
                            border = BorderStroke(1.5.dp, BrandEmerald),
                            modifier = Modifier.weight(1f)
                        ) {
                            Column(modifier = Modifier.padding(14.dp)) {
                                Text(text = "💵 Cash Sales", fontWeight = FontWeight.Bold, color = BrandDark, fontSize = 13.sp)
                                Spacer(modifier = Modifier.height(4.dp))
                                Text(
                                    text = CurrencyFormatter.formatInr(report.stats.cashSales),
                                    fontWeight = FontWeight.ExtraBold,
                                    color = BrandEmerald,
                                    fontSize = 18.sp
                                )
                            }
                        }

                        Card(
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandSurface),
                            border = BorderStroke(1.5.dp, BrandOrange),
                            modifier = Modifier.weight(1f)
                        ) {
                            Column(modifier = Modifier.padding(14.dp)) {
                                Text(text = "📱 UPI / Online", fontWeight = FontWeight.Bold, color = BrandDark, fontSize = 13.sp)
                                Spacer(modifier = Modifier.height(4.dp))
                                Text(
                                    text = CurrencyFormatter.formatInr(report.stats.onlineSales),
                                    fontWeight = FontWeight.ExtraBold,
                                    color = BrandOrangeDark,
                                    fontSize = 18.sp
                                )
                            }
                        }
                    }

                    // Comparison Insights
                    Text(text = "SALES COMPARISON BENCHMARKS", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = TextSecondary)

                    ComparisonRow(label = "Today", stats = report.todayStats)
                    ComparisonRow(label = "Yesterday", stats = report.yesterdayStats)
                    ComparisonRow(label = "This Month", stats = report.thisMonthStats)
                    ComparisonRow(label = "Last Month", stats = report.lastMonthStats)
                    ComparisonRow(label = "Yearly Total", stats = report.yearStats)
                }
            }
        }
    }
}

@Composable
private fun ComparisonRow(label: String, stats: DailyStats) {
    Card(
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = BrandSurface),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier.padding(14.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column {
                Text(text = label, fontWeight = FontWeight.Bold, color = BrandDark)
                Text(text = "${stats.totalOrders} orders", style = MaterialTheme.typography.bodySmall, color = TextSecondary)
            }
            Text(
                text = CurrencyFormatter.formatInr(stats.totalSales),
                fontWeight = FontWeight.ExtraBold,
                color = BrandDark
            )
        }
    }
}
