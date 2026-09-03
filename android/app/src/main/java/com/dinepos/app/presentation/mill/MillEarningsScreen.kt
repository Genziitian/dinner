package com.dinepos.app.presentation.mill

import android.app.DatePickerDialog
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.outlined.Assignment
import androidx.compose.material.icons.filled.ChevronLeft
import androidx.compose.material.icons.filled.ChevronRight
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.MillEarningsResponseDto
import com.dinepos.app.data.dto.MillOrderDto
import com.dinepos.app.data.dto.MillServiceBreakdownDto
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MillEarningsScreen(
    onNavigateBack: (() -> Unit)? = null
) {
    val context = LocalContext.current
    val millRepository = DinePosApp.instance.millRepository
    val scope = rememberCoroutineScope()

    val isoDateFormat = remember { SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()) }
    val displayDateFormat = remember { SimpleDateFormat("EEE, dd MMM yyyy", Locale.getDefault()) }

    var selectedPeriod by remember { mutableStateOf("today") } // "today", "yesterday", "month", "custom"
    var customDate by remember { mutableStateOf(isoDateFormat.format(Date())) }
    var earningsData by remember { mutableStateOf<MillEarningsResponseDto?>(null) }
    var isLoading by remember { mutableStateOf(true) }
    var errorMessage by remember { mutableStateOf<String?>(null) }

    fun loadEarnings() {
        scope.launch {
            isLoading = true
            errorMessage = null
            val res = millRepository.getEarnings(
                period = selectedPeriod,
                date = if (selectedPeriod == "custom") customDate else null
            )
            when (res) {
                is Resource.Success -> {
                    earningsData = res.data
                    isLoading = false
                }
                is Resource.Error -> {
                    errorMessage = res.message ?: "Failed to load earnings."
                    isLoading = false
                }
                else -> {
                    isLoading = false
                }
            }
        }
    }

    LaunchedEffect(selectedPeriod, customDate) {
        loadEarnings()
    }

    fun openDatePicker() {
        val cal = Calendar.getInstance()
        try {
            isoDateFormat.parse(customDate)?.let { cal.time = it }
        } catch (_: Exception) {}

        DatePickerDialog(
            context,
            { _, year, month, dayOfMonth ->
                val chosen = Calendar.getInstance().apply {
                    set(year, month, dayOfMonth)
                }
                customDate = isoDateFormat.format(chosen.time)
                selectedPeriod = "custom"
            },
            cal.get(Calendar.YEAR),
            cal.get(Calendar.MONTH),
            cal.get(Calendar.DAY_OF_MONTH)
        ).show()
    }

    fun shiftCustomDate(days: Int) {
        val cal = Calendar.getInstance()
        try {
            isoDateFormat.parse(customDate)?.let { cal.time = it }
        } catch (_: Exception) {}
        cal.add(Calendar.DAY_OF_MONTH, days)
        customDate = isoDateFormat.format(cal.time)
        selectedPeriod = "custom"
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Mill Earnings",
                        style = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                },
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
                actions = {
                    IconButton(onClick = { loadEarnings() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = BrandDark)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandBackground)
            )
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
        ) {
            // Period Filter Pills (Today, Yesterday, Month, Custom Date)
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .horizontalScroll(rememberScrollState())
                    .padding(horizontal = 16.dp, vertical = 6.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                PeriodFilterChip(
                    label = "Today",
                    isSelected = selectedPeriod == "today",
                    onClick = { selectedPeriod = "today" }
                )
                PeriodFilterChip(
                    label = "Yesterday",
                    isSelected = selectedPeriod == "yesterday",
                    onClick = { selectedPeriod = "yesterday" }
                )
                PeriodFilterChip(
                    label = "This Month",
                    isSelected = selectedPeriod == "month",
                    onClick = { selectedPeriod = "month" }
                )
                PeriodFilterChip(
                    label = if (selectedPeriod == "custom") "Date: $customDate" else "Custom Date",
                    isSelected = selectedPeriod == "custom",
                    icon = Icons.Outlined.CalendarToday,
                    onClick = {
                        if (selectedPeriod != "custom") {
                            selectedPeriod = "custom"
                        } else {
                            openDatePicker()
                        }
                    }
                )
            }

            // If Custom Date is selected, show date stepper bar
            if (selectedPeriod == "custom") {
                Surface(
                    color = Color.White,
                    shape = RoundedCornerShape(12.dp),
                    border = BorderStroke(1.dp, BrandBorder),
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 4.dp)
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(horizontal = 8.dp, vertical = 6.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        IconButton(
                            onClick = { shiftCustomDate(-1) },
                            modifier = Modifier.size(36.dp)
                        ) {
                            Icon(Icons.Default.ChevronLeft, contentDescription = "Previous Day", tint = BrandDark)
                        }

                        Row(
                            modifier = Modifier
                                .clip(RoundedCornerShape(8.dp))
                                .clickable { openDatePicker() }
                                .padding(horizontal = 12.dp, vertical = 6.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Icon(Icons.Outlined.Event, contentDescription = null, tint = BrandOrange, modifier = Modifier.size(16.dp))
                            Spacer(modifier = Modifier.width(8.dp))
                            Text(
                                text = try {
                                    isoDateFormat.parse(customDate)?.let { displayDateFormat.format(it) } ?: customDate
                                } catch (_: Exception) {
                                    customDate
                                },
                                fontWeight = FontWeight.Bold,
                                fontSize = 14.sp,
                                color = BrandDark
                            )
                        }

                        IconButton(
                            onClick = { shiftCustomDate(1) },
                            modifier = Modifier.size(36.dp)
                        ) {
                            Icon(Icons.Default.ChevronRight, contentDescription = "Next Day", tint = BrandDark)
                        }
                    }
                }
            }

            Spacer(modifier = Modifier.height(4.dp))

            // Main Content Area
            if (isLoading) {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .weight(1f),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator(color = BrandOrange)
                }
            } else if (errorMessage != null) {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .weight(1f)
                        .padding(24.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(text = errorMessage ?: "Error", color = StatusError, textAlign = TextAlign.Center)
                        Spacer(modifier = Modifier.height(12.dp))
                        Button(
                            onClick = { loadEarnings() },
                            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                        ) {
                            Text("Retry")
                        }
                    }
                }
            } else {
                val data = earningsData
                val summary = data?.summary
                val services = data?.services ?: emptyList()
                val orders = data?.orders ?: emptyList()

                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .weight(1f),
                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                    verticalArrangement = Arrangement.spacedBy(14.dp)
                ) {
                    // 1. Hero Total Earnings Card
                    item {
                        Card(
                            shape = RoundedCornerShape(20.dp),
                            colors = CardDefaults.cardColors(containerColor = Color(0xFF1E293B)),
                            elevation = CardDefaults.cardElevation(defaultElevation = 3.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Column(modifier = Modifier.padding(20.dp)) {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text(
                                        text = "TOTAL EARNING",
                                        fontSize = 12.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = Color(0xFF94A3B8),
                                        letterSpacing = 1.sp
                                    )
                                    Surface(
                                        color = Color.White.copy(alpha = 0.12f),
                                        shape = RoundedCornerShape(50)
                                    ) {
                                        Text(
                                            text = data?.dateLabel ?: "",
                                            fontSize = 11.sp,
                                            fontWeight = FontWeight.Medium,
                                            color = Color.White,
                                            modifier = Modifier.padding(horizontal = 10.dp, vertical = 3.dp)
                                        )
                                    }
                                }

                                Spacer(modifier = Modifier.height(10.dp))

                                Text(
                                    text = String.format(Locale.getDefault(), "Rs. %.2f", summary?.totalEarnings ?: 0.0),
                                    fontSize = 34.sp,
                                    fontWeight = FontWeight.ExtraBold,
                                    color = Color.White
                                )

                                Spacer(modifier = Modifier.height(16.dp))
                                HorizontalDivider(color = Color.White.copy(alpha = 0.15f))
                                Spacer(modifier = Modifier.height(14.dp))

                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween
                                ) {
                                    Column {
                                        Text(text = "Collected (Paid)", fontSize = 11.sp, color = Color(0xFF94A3B8))
                                        Text(
                                            text = String.format(Locale.getDefault(), "Rs. %.2f", summary?.paidEarnings ?: 0.0),
                                            fontSize = 16.sp,
                                            fontWeight = FontWeight.Bold,
                                            color = Color(0xFF10B981)
                                        )
                                    }
                                    Column(horizontalAlignment = Alignment.End) {
                                        Text(text = "Pending Dues", fontSize = 11.sp, color = Color(0xFF94A3B8))
                                        Text(
                                            text = String.format(Locale.getDefault(), "Rs. %.2f", summary?.unpaidEarnings ?: 0.0),
                                            fontSize = 16.sp,
                                            fontWeight = FontWeight.Bold,
                                            color = if ((summary?.unpaidEarnings ?: 0.0) > 0) Color(0xFFF59E0B) else Color(0xFF94A3B8)
                                        )
                                    }
                                }
                            }
                        }
                    }

                    // 2. Metrics Row (Total Weight Ground & Total Orders)
                    item {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            MetricSummaryCard(
                                title = "Total Weight Ground",
                                value = String.format(Locale.getDefault(), "%.1f KG", summary?.totalWeightKg ?: 0.0),
                                icon = Icons.Outlined.Scale,
                                tint = BrandOrange,
                                modifier = Modifier.weight(1f)
                            )
                            MetricSummaryCard(
                                title = "Total Orders",
                                value = "${summary?.totalOrders ?: 0}",
                                icon = Icons.AutoMirrored.Outlined.Assignment,
                                tint = Color(0xFF3B82F6),
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }

                    // 3. Earnings by Grain / Service Breakdown
                    if (services.isNotEmpty()) {
                        item {
                            Text(
                                text = "Earnings by Grain / Service",
                                fontSize = 15.sp,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark,
                                modifier = Modifier.padding(top = 4.dp, bottom = 2.dp)
                            )
                        }

                        items(services) { svc ->
                            ServiceBreakdownRow(service = svc, totalRevenue = summary?.totalEarnings ?: 1.0)
                        }
                    }

                    // 4. Period Orders Header
                    item {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(top = 8.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "Orders List (${orders.size})",
                                fontSize = 15.sp,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                        }
                    }

                    // 5. Orders List
                    if (orders.isEmpty()) {
                        item {
                            Surface(
                                color = Color.White,
                                shape = RoundedCornerShape(14.dp),
                                border = BorderStroke(1.dp, BrandBorder),
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .padding(28.dp),
                                    contentAlignment = Alignment.Center
                                ) {
                                    Text(
                                        text = "No orders found for this period.",
                                        fontSize = 13.sp,
                                        color = TextMuted,
                                        textAlign = TextAlign.Center
                                    )
                                }
                            }
                        }
                    } else {
                        items(orders) { order ->
                            EarningOrderRow(order = order)
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun PeriodFilterChip(
    label: String,
    isSelected: Boolean,
    icon: androidx.compose.ui.graphics.vector.ImageVector? = null,
    onClick: () -> Unit
) {
    Surface(
        onClick = onClick,
        shape = RoundedCornerShape(50),
        color = if (isSelected) BrandOrange else Color.White,
        border = BorderStroke(1.dp, if (isSelected) BrandOrange else BrandBorder)
    ) {
        Row(
            modifier = Modifier.padding(horizontal = 14.dp, vertical = 7.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            if (icon != null) {
                Icon(
                    imageVector = icon,
                    contentDescription = null,
                    tint = if (isSelected) Color.White else TextSecondary,
                    modifier = Modifier.size(14.dp)
                )
                Spacer(modifier = Modifier.width(6.dp))
            }
            Text(
                text = label,
                fontSize = 12.5.sp,
                fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
                color = if (isSelected) Color.White else BrandDark
            )
        }
    }
}

@Composable
fun MetricSummaryCard(
    title: String,
    value: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    tint: Color,
    modifier: Modifier = Modifier
) {
    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = modifier
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            Box(
                modifier = Modifier
                    .size(36.dp)
                    .clip(CircleShape)
                    .background(tint.copy(alpha = 0.12f)),
                contentAlignment = Alignment.Center
            ) {
                Icon(imageVector = icon, contentDescription = null, tint = tint, modifier = Modifier.size(18.dp))
            }
            Spacer(modifier = Modifier.height(10.dp))
            Text(text = title, fontSize = 11.5.sp, color = TextMuted)
            Spacer(modifier = Modifier.height(2.dp))
            Text(
                text = value,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                color = BrandDark
            )
        }
    }
}

@Composable
fun ServiceBreakdownRow(
    service: MillServiceBreakdownDto,
    totalRevenue: Double
) {
    val share = if (totalRevenue > 0) ((service.totalAmount / totalRevenue) * 100).toInt() else 0

    Surface(
        color = Color.White,
        shape = RoundedCornerShape(14.dp),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = service.serviceName,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Text(
                        text = "${service.orderCount} orders • ${String.format(Locale.getDefault(), "%.1f", service.totalWeightKg)} KG ground",
                        fontSize = 11.5.sp,
                        color = TextSecondary
                    )
                }
                Column(horizontalAlignment = Alignment.End) {
                    Text(
                        text = String.format(Locale.getDefault(), "Rs. %.2f", service.totalAmount),
                        fontSize = 15.sp,
                        fontWeight = FontWeight.ExtraBold,
                        color = BrandDark
                    )
                    Text(
                        text = "$share% of revenue",
                        fontSize = 11.sp,
                        color = BrandOrange,
                        fontWeight = FontWeight.Medium
                    )
                }
            }

            Spacer(modifier = Modifier.height(8.dp))
            LinearProgressIndicator(
                progress = { (share / 100f).coerceIn(0f, 1f) },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(5.dp)
                    .clip(RoundedCornerShape(3.dp)),
                color = BrandOrange,
                trackColor = BrandBorder.copy(alpha = 0.5f),
            )
        }
    }
}

@Composable
fun EarningOrderRow(order: MillOrderDto) {
    val isPaid = order.paymentStatus.equals("paid", ignoreCase = true)

    Surface(
        color = Color.White,
        shape = RoundedCornerShape(12.dp),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(
                        text = "Order #${if (order.orderNumber > 0) order.orderNumber else order.id}",
                        fontWeight = FontWeight.Bold,
                        fontSize = 13.sp,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Surface(
                        color = if (isPaid) Color(0xFFD1FAE5) else Color(0xFFFEF3C7),
                        shape = RoundedCornerShape(4.dp)
                    ) {
                        Text(
                            text = if (isPaid) "Paid" else "Unpaid",
                            fontSize = 10.sp,
                            fontWeight = FontWeight.Bold,
                            color = if (isPaid) Color(0xFF065F46) else Color(0xFF92400E),
                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                        )
                    }
                }
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = "${order.customerName} • ${order.serviceName} (${order.weightKg} KG)",
                    fontSize = 11.5.sp,
                    color = TextSecondary
                )
            }

            Text(
                text = String.format(Locale.getDefault(), "Rs. %.2f", order.totalAmount),
                fontWeight = FontWeight.Bold,
                fontSize = 14.sp,
                color = BrandDark
            )
        }
    }
}
