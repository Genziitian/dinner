package com.dinepos.app.presentation.mill

import android.app.DatePickerDialog
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.automirrored.filled.KeyboardArrowLeft
import androidx.compose.material.icons.automirrored.filled.KeyboardArrowRight
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.MillOrderDto
import kotlinx.coroutines.launch
import java.net.URLEncoder
import java.text.SimpleDateFormat
import java.util.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MillOrdersScreen(
    onNavigateBack: () -> Unit
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val millRepository = DinePosApp.instance.millRepository
    val restaurantName = DinePosApp.instance.sessionManager.getRestaurantName().ifBlank { "Atta Mill" }

    val isoDateFormat = remember { SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()) }
    val displayDateFormat = remember { SimpleDateFormat("dd MMM yyyy", Locale.getDefault()) }

    val todayStr = remember { isoDateFormat.format(Date()) }
    var selectedDate by remember { mutableStateOf<String?>(todayStr) }

    var orders by remember { mutableStateOf<List<MillOrderDto>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var searchQuery by remember { mutableStateOf("") }
    var selectedFilter by remember { mutableStateOf("all") }

    val filterOptions = listOf(
        "all" to "All",
        "received" to "Received",
        "processing" to "In Process",
        "ready" to "Ready",
        "delivered" to "Delivered",
        "unpaid" to "Unpaid"
    )

    fun loadOrders() {
        scope.launch {
            isLoading = true
            val statusParam = if (selectedFilter == "unpaid") null else if (selectedFilter == "all") null else selectedFilter
            when (val res = millRepository.getOrders(status = statusParam, search = searchQuery.ifBlank { null }, date = selectedDate)) {
                is Resource.Success -> {
                    var list = res.data ?: emptyList()
                    if (selectedFilter == "unpaid") {
                        list = list.filter { it.paymentStatus.equals("unpaid", ignoreCase = true) }
                    }
                    orders = list
                }
                is Resource.Error -> {
                    Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
                }
                else -> {}
            }
            isLoading = false
        }
    }

    LaunchedEffect(selectedFilter, selectedDate) {
        loadOrders()
    }

    fun showDatePicker() {
        val cal = Calendar.getInstance()
        selectedDate?.let {
            try {
                isoDateFormat.parse(it)?.let { date -> cal.time = date }
            } catch (e: Exception) {}
        }

        DatePickerDialog(
            context,
            { _, year, month, dayOfMonth ->
                val newCal = Calendar.getInstance().apply {
                    set(year, month, dayOfMonth)
                }
                selectedDate = isoDateFormat.format(newCal.time)
            },
            cal.get(Calendar.YEAR),
            cal.get(Calendar.MONTH),
            cal.get(Calendar.DAY_OF_MONTH)
        ).show()
    }

    fun shiftDate(days: Int) {
        val cal = Calendar.getInstance()
        selectedDate?.let {
            try {
                isoDateFormat.parse(it)?.let { date -> cal.time = date }
            } catch (e: Exception) {}
        }
        cal.add(Calendar.DAY_OF_MONTH, days)
        selectedDate = isoDateFormat.format(cal.time)
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Grinding Orders",
                        style = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(
                            imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                            contentDescription = "Back",
                            tint = BrandDark
                        )
                    }
                },
                actions = {
                    IconButton(onClick = { loadOrders() }) {
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
            // Search Bar (Searches: order id, order number, name, phone)
            OutlinedTextField(
                value = searchQuery,
                onValueChange = {
                    searchQuery = it
                    loadOrders()
                },
                placeholder = { Text("Search order #, ID, name, or phone...") },
                leadingIcon = { Icon(Icons.Default.Search, contentDescription = null, tint = TextSecondary) },
                singleLine = true,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 6.dp)
            )

            // Date Selector Bar
            Card(
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                border = BorderStroke(1.dp, BrandBorder),
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 4.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 8.dp, vertical = 4.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    IconButton(
                        onClick = { shiftDate(-1) },
                        modifier = Modifier.size(36.dp)
                    ) {
                        Icon(
                            imageVector = Icons.AutoMirrored.Filled.KeyboardArrowLeft,
                            contentDescription = "Previous Day",
                            tint = BrandDark
                        )
                    }

                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier
                            .clickable { showDatePicker() }
                            .padding(horizontal = 12.dp, vertical = 6.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.CalendarMonth,
                            contentDescription = "Pick Date",
                            tint = BrandOrange,
                            modifier = Modifier.size(18.dp)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        val formattedDateText = if (selectedDate == null) {
                            "All Dates"
                        } else if (selectedDate == todayStr) {
                            "Today (${displayDateFormat.format(Date())})"
                        } else {
                            try {
                                isoDateFormat.parse(selectedDate!!)?.let { displayDateFormat.format(it) } ?: selectedDate!!
                            } catch (e: Exception) {
                                selectedDate!!
                            }
                        }
                        Text(
                            text = formattedDateText,
                            fontWeight = FontWeight.Bold,
                            fontSize = 14.sp,
                            color = BrandDark
                        )
                    }

                    IconButton(
                        onClick = { shiftDate(1) },
                        modifier = Modifier.size(36.dp)
                    ) {
                        Icon(
                            imageVector = Icons.AutoMirrored.Filled.KeyboardArrowRight,
                            contentDescription = "Next Day",
                            tint = BrandDark
                        )
                    }
                }
            }

            // Quick Date Filter Pills: Today / Yesterday / All Dates
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 2.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                val isToday = selectedDate == todayStr
                FilterChip(
                    selected = isToday,
                    onClick = { selectedDate = todayStr },
                    label = { Text("Today", fontSize = 12.sp, fontWeight = if (isToday) FontWeight.Bold else FontWeight.Normal) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = BrandOrange.copy(alpha = 0.12f),
                        selectedLabelColor = BrandOrange
                    )
                )

                val calYesterday = Calendar.getInstance().apply { add(Calendar.DAY_OF_MONTH, -1) }
                val yesterdayStr = isoDateFormat.format(calYesterday.time)
                val isYesterday = selectedDate == yesterdayStr
                FilterChip(
                    selected = isYesterday,
                    onClick = { selectedDate = yesterdayStr },
                    label = { Text("Yesterday", fontSize = 12.sp, fontWeight = if (isYesterday) FontWeight.Bold else FontWeight.Normal) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = BrandOrange.copy(alpha = 0.12f),
                        selectedLabelColor = BrandOrange
                    )
                )

                val isAllDates = selectedDate == null
                FilterChip(
                    selected = isAllDates,
                    onClick = { selectedDate = null },
                    label = { Text("All Dates", fontSize = 12.sp, fontWeight = if (isAllDates) FontWeight.Bold else FontWeight.Normal) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = BrandOrange.copy(alpha = 0.12f),
                        selectedLabelColor = BrandOrange
                    )
                )
            }

            // Status Filter Chips
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .horizontalScroll(rememberScrollState())
                    .padding(horizontal = 16.dp, vertical = 6.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                filterOptions.forEach { (key, label) ->
                    val isSelected = selectedFilter == key
                    FilterChip(
                        selected = isSelected,
                        onClick = { selectedFilter = key },
                        label = { Text(label, fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal) },
                        colors = FilterChipDefaults.filterChipColors(
                            selectedContainerColor = BrandOrange.copy(alpha = 0.15f),
                            selectedLabelColor = BrandOrange
                        ),
                        border = FilterChipDefaults.filterChipBorder(
                            enabled = true,
                            selected = isSelected,
                            borderColor = if (isSelected) BrandOrange else BrandBorder
                        )
                    )
                }
            }

            if (isLoading) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = BrandOrange)
                }
            } else if (orders.isEmpty()) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text(
                        text = "No grinding orders found.",
                        color = TextSecondary,
                        fontSize = 15.sp
                    )
                }
            } else {
                LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    items(orders, key = { it.id }) { order ->
                        MillOrderCard(
                            order = order,
                            restaurantName = restaurantName,
                            onUpdateStatus = { newStatus ->
                                scope.launch {
                                    when (val res = millRepository.updateOrderStatus(order.id, newStatus)) {
                                        is Resource.Success -> loadOrders()
                                        is Resource.Error -> Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
                                        else -> {}
                                    }
                                }
                            },
                            onMarkPaid = {
                                scope.launch {
                                    when (val res = millRepository.updateOrderPayment(order.id, "paid", "cash")) {
                                        is Resource.Success -> loadOrders()
                                        is Resource.Error -> Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
                                        else -> {}
                                    }
                                }
                            }
                        )
                    }
                }
            }
        }
    }
}

@Composable
private fun MillOrderCard(
    order: MillOrderDto,
    restaurantName: String,
    onUpdateStatus: (String) -> Unit,
    onMarkPaid: () -> Unit
) {
    val context = LocalContext.current
    val isPaid = order.paymentStatus.equals("paid", ignoreCase = true)

    val statusColor = when (order.status.lowercase()) {
        "received" -> Color(0xFF3B82F6)
        "processing" -> BrandAmber
        "ready" -> BrandEmerald
        "delivered" -> BrandDark
        else -> TextMuted
    }

    val nextStatus = when (order.status.lowercase()) {
        "received" -> "processing" to "Mark In Process"
        "processing" -> "ready" to "Mark Ready"
        "ready" -> "delivered" to "Mark Delivered"
        else -> null
    }

    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = BrandSurface),
        border = BorderStroke(1.dp, BrandBorder),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            // Top Row: Daily Order # & Status Badge
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text(
                        text = "Order #${order.orderNumber}",
                        fontWeight = FontWeight.Black,
                        fontSize = 18.sp,
                        color = BrandDark
                    )
                    Text(
                        text = "ID: #${order.id} • ${order.orderDate}${order.orderTime?.let { " $it" } ?: ""}",
                        fontSize = 11.sp,
                        color = TextSecondary
                    )
                }

                Surface(
                    shape = RoundedCornerShape(6.dp),
                    color = statusColor.copy(alpha = 0.12f),
                    border = BorderStroke(1.dp, statusColor.copy(alpha = 0.4f))
                ) {
                    Text(
                        text = order.status.uppercase(),
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = statusColor,
                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                    )
                }
            }

            Spacer(modifier = Modifier.height(8.dp))

            // Customer Info
            val custText = if (order.customerPhone.isNotBlank()) {
                "${order.customerName} • ${order.customerPhone}"
            } else {
                order.customerName
            }
            Text(
                text = custText,
                fontWeight = FontWeight.SemiBold,
                fontSize = 14.sp,
                color = BrandDark
            )

            // Service & Weight
            Text(
                text = "${order.serviceName} • ${order.weightKg} KG @ Rs.${order.ratePerKg}/KG",
                fontSize = 13.sp,
                color = TextSecondary
            )

            if (!order.notes.isNullOrBlank()) {
                Text(
                    text = "Note: ${order.notes}",
                    fontSize = 12.sp,
                    color = TextMuted
                )
            }

            Spacer(modifier = Modifier.height(10.dp))
            HorizontalDivider(color = BrandBorder.copy(alpha = 0.5f))
            Spacer(modifier = Modifier.height(10.dp))

            // Price & Payment Row
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text(
                        text = CurrencyFormatter.formatInr(order.totalAmount),
                        fontWeight = FontWeight.Black,
                        fontSize = 18.sp,
                        color = BrandDark
                    )
                    Text(
                        text = if (isPaid) "Paid via ${order.paymentMethod.uppercase()}" else "Pending Due",
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = if (isPaid) BrandEmerald else BrandAmber
                    )
                }

                // Actions Row: WhatsApp & Next Action
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    // WhatsApp Button (Only if phone entered)
                    if (order.customerPhone.isNotBlank()) {
                        IconButton(
                            onClick = {
                                val msg = "Namaste ${order.customerName} ji, your order #${order.orderNumber} (${order.serviceName}, ${order.weightKg} KG) is ${order.status} at $restaurantName. Total amount: Rs.${order.totalAmount}."
                                val cleanPhone = order.customerPhone.replace(Regex("[^0-9]"), "")
                                val url = "https://api.whatsapp.com/send?phone=91$cleanPhone&text=${URLEncoder.encode(msg, "UTF-8")}"
                                try {
                                    val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                                    context.startActivity(intent)
                                } catch (e: Exception) {
                                    Toast.makeText(context, "WhatsApp not installed", Toast.LENGTH_SHORT).show()
                                }
                            }
                        ) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.Chat,
                                contentDescription = "WhatsApp",
                                tint = Color(0xFF25D366)
                            )
                        }
                    }

                    if (!isPaid) {
                        OutlinedButton(
                            onClick = onMarkPaid,
                            contentPadding = PaddingValues(horizontal = 10.dp, vertical = 4.dp),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text("Mark Paid", fontSize = 12.sp, color = BrandEmerald)
                        }
                    }

                    if (nextStatus != null) {
                        Button(
                            onClick = { onUpdateStatus(nextStatus.first) },
                            contentPadding = PaddingValues(horizontal = 12.dp, vertical = 4.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text(nextStatus.second, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }
        }
    }
}
