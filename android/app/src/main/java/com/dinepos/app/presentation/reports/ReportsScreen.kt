package com.dinepos.app.presentation.reports

import android.widget.Toast
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
import androidx.compose.material.icons.outlined.FileDownload
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.ExportDownloadHelper
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.DailyStats
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReportsScreen(
    onNavigateBack: () -> Unit,
    viewModel: ReportsViewModel = viewModel()
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val uiState by viewModel.uiState.collectAsState()
    val report = uiState.report

    var isExporting by remember { mutableStateOf(false) }

    fun exportCurrentReport() {
        scope.launch {
            isExporting = true
            val selectedPeriod = uiState.selectedPeriod
            val exportType = if (selectedPeriod == "today") "daily" else if (selectedPeriod == "this_month") "monthly" else "custom"
            val todayDate = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date())
            val currentMonth = SimpleDateFormat("yyyy-MM", Locale.getDefault()).format(Date())

            when (val res = DinePosApp.instance.managerRepository.getExportData(
                type = exportType,
                date = if (exportType == "daily") todayDate else null,
                month = if (exportType == "monthly") currentMonth else null
            )) {
                is Resource.Success -> {
                    isExporting = false
                    val csv = ExportDownloadHelper.buildSalesCsv(res.data, isMill = false)
                    val filename = ExportDownloadHelper.generateFilename(
                        businessName = res.data.restaurantName.ifBlank { "SalesReport" },
                        type = "FinancialReport",
                        rangeLabel = selectedPeriod,
                        extension = "csv"
                    )
                    val dlResult = ExportDownloadHelper.saveToDownloads(context, filename, csv, "text/csv")
                    if (dlResult.success) {
                        Toast.makeText(context, "Report saved to Downloads/DinePOS: $filename", Toast.LENGTH_SHORT).show()
                        if (dlResult.contentUri != null) {
                            ExportDownloadHelper.shareFile(context, dlResult.contentUri, "Sales Report ($selectedPeriod)")
                        }
                    } else {
                        Toast.makeText(context, dlResult.message, Toast.LENGTH_LONG).show()
                    }
                }
                is Resource.Error -> {
                    isExporting = false
                    Toast.makeText(context, res.message ?: "Failed to export report", Toast.LENGTH_LONG).show()
                }
                else -> {
                    isExporting = false
                }
            }
        }
    }

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
                    IconButton(onClick = { exportCurrentReport() }, enabled = !isExporting) {
                        if (isExporting) {
                            CircularProgressIndicator(modifier = Modifier.size(18.dp), color = BrandOrange)
                        } else {
                            Icon(Icons.Outlined.FileDownload, contentDescription = "Export CSV", tint = BrandDark)
                        }
                    }
                    IconButton(onClick = { viewModel.loadReports() }) {
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
                .padding(16.dp)
        ) {
            val isTablet = maxWidth >= 600.dp

            Column(modifier = Modifier.fillMaxSize()) {
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
                        // Metric Cards
                        if (isTablet) {
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
                                        Text(
                                            text = "TOTAL SALES (${report.currentPeriod.uppercase()})",
                                            color = TextMuted,
                                            fontSize = 11.sp,
                                            fontWeight = FontWeight.Bold
                                        )
                                        Spacer(modifier = Modifier.height(4.dp))
                                        Text(
                                            text = CurrencyFormatter.formatInr(report.stats.totalSales),
                                            color = Color.White,
                                            fontSize = 24.sp,
                                            fontWeight = FontWeight.Black
                                        )
                                        Spacer(modifier = Modifier.height(6.dp))
                                        Text(
                                            text = "${report.stats.totalOrders} orders completed",
                                            color = BrandAmber,
                                            fontWeight = FontWeight.SemiBold,
                                            fontSize = 12.sp
                                        )
                                    }
                                }

                                Card(
                                    shape = RoundedCornerShape(16.dp),
                                    colors = CardDefaults.cardColors(containerColor = BrandEmeraldLight),
                                    border = BorderStroke(1.5.dp, BrandEmerald.copy(alpha = 0.4f)),
                                    modifier = Modifier.weight(1f)
                                ) {
                                    Column(modifier = Modifier.padding(18.dp)) {
                                        Text(text = "💵 CASH SALES", fontWeight = FontWeight.Bold, color = BrandEmerald, fontSize = 11.sp)
                                        Spacer(modifier = Modifier.height(4.dp))
                                        Text(
                                            text = CurrencyFormatter.formatInr(report.stats.cashSales),
                                            fontWeight = FontWeight.Black,
                                            color = BrandEmerald,
                                            fontSize = 24.sp
                                        )
                                        Spacer(modifier = Modifier.height(6.dp))
                                        val cashPercent = if (report.stats.totalSales > 0) ((report.stats.cashSales / report.stats.totalSales) * 100).toInt() else 0
                                        Text(text = "$cashPercent% of period sales", color = TextSecondary, fontSize = 12.sp)
                                    }
                                }

                                Card(
                                    shape = RoundedCornerShape(16.dp),
                                    colors = CardDefaults.cardColors(containerColor = BrandOrangeLight),
                                    border = BorderStroke(1.5.dp, BrandOrange.copy(alpha = 0.4f)),
                                    modifier = Modifier.weight(1f)
                                ) {
                                    Column(modifier = Modifier.padding(18.dp)) {
                                        Text(text = "📱 UPI / ONLINE SALES", fontWeight = FontWeight.Bold, color = BrandOrange, fontSize = 11.sp)
                                        Spacer(modifier = Modifier.height(4.dp))
                                        Text(
                                            text = CurrencyFormatter.formatInr(report.stats.onlineSales),
                                            fontWeight = FontWeight.Black,
                                            color = BrandOrangeDark,
                                            fontSize = 24.sp
                                        )
                                        Spacer(modifier = Modifier.height(6.dp))
                                        val upiPercent = if (report.stats.totalSales > 0) ((report.stats.onlineSales / report.stats.totalSales) * 100).toInt() else 0
                                        Text(text = "$upiPercent% of period sales", color = TextSecondary, fontSize = 12.sp)
                                    }
                                }
                            }
                        } else {
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

                            // Payment Breakdown Row
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
                        }

                        // Comparison Insights
                        Text(text = "SALES COMPARISON BENCHMARKS", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = TextSecondary)

                        if (isTablet) {
                            // 2-Column Comparison Layout for Tablets
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(10.dp)
                            ) {
                                Column(modifier = Modifier.weight(1f), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                                    ComparisonRow(label = "Today", stats = report.todayStats)
                                    ComparisonRow(label = "Yesterday", stats = report.yesterdayStats)
                                    ComparisonRow(label = "Yearly Total", stats = report.yearStats)
                                }
                                Column(modifier = Modifier.weight(1f), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                                    ComparisonRow(label = "This Month", stats = report.thisMonthStats)
                                    ComparisonRow(label = "Last Month", stats = report.lastMonthStats)
                                }
                            }
                        } else {
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
