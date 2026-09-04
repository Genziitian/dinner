package com.dinepos.app.presentation.mill

import android.app.DatePickerDialog
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.ExperimentalFoundationApi
import androidx.compose.foundation.combinedClickable
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
import androidx.compose.material.icons.filled.ArrowDropDown
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.CalendarToday
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.FilterList
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
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
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.localization.L10n
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.core.utils.WhatsAppHelper
import com.dinepos.app.data.dto.MillOrderDto
import kotlinx.coroutines.launch
import java.net.URLEncoder
import java.text.SimpleDateFormat
import java.util.*

@OptIn(ExperimentalMaterial3Api::class, ExperimentalFoundationApi::class)
@Composable
fun MillOrdersScreen(
    onNavigateBack: () -> Unit
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val millRepository = DinePosApp.instance.millRepository
    val sessionManager = DinePosApp.instance.sessionManager
    val restaurantName = sessionManager.getRestaurantName().ifBlank { "Atta Mill" }

    val isoDateFormat = remember { SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()) }
    val displayDateFormat = remember { SimpleDateFormat("dd MMM yyyy", Locale.getDefault()) }

    val todayStr = remember { isoDateFormat.format(Date()) }
    var selectedDate by remember { mutableStateOf<String?>(todayStr) }

    val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"

    var orders by remember { mutableStateOf<List<MillOrderDto>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var searchQuery by remember { mutableStateOf("") }
    var selectedFilter by remember { mutableStateOf("all") }
    var isSearchActive by remember { mutableStateOf(false) }

    // State for hold action menu (Hide / Unhide / Delete)
    var hiddenVersion by remember { mutableStateOf(0) }
    var actionMenuOrder by remember { mutableStateOf<MillOrderDto?>(null) }
    var deleteConfirmOrder by remember { mutableStateOf<MillOrderDto?>(null) }

    // State for Order Ready / Delivered notification modal
    var notifyModalOrder by remember { mutableStateOf<Pair<MillOrderDto, String>?>(null) }

    val filterOptions = listOf(
        "all" to com.dinepos.app.core.localization.L10n.allStatus(isHi),
        "received" to com.dinepos.app.core.localization.L10n.received(isHi),
        "ready" to com.dinepos.app.core.localization.L10n.ready(isHi),
        "delivered" to com.dinepos.app.core.localization.L10n.delivered(isHi),
        "unpaid" to com.dinepos.app.core.localization.L10n.unpaid(isHi)
    )

    fun loadOrders() {
        scope.launch {
            isLoading = true
            val statusParam = if (selectedFilter == "unpaid") null else if (selectedFilter == "all") null else selectedFilter
            when (val res = millRepository.getOrders(status = statusParam, search = searchQuery.ifBlank { null }, date = selectedDate)) {
                is Resource.Success -> {
                    var list = res.data ?: emptyList()
                    val deletedIds = sessionManager.getDeletedOrderIds()
                    list = list.filter { !deletedIds.contains(it.id.toString()) && !it.status.equals("cancelled", ignoreCase = true) }
                    list = list.map { o ->
                        if (o.status.equals("delivered", ignoreCase = true) && !o.paymentStatus.equals("paid", ignoreCase = true)) {
                            scope.launch {
                                millRepository.updateOrderPayment(o.id, "paid", "cash")
                            }
                            o.copy(paymentStatus = "paid")
                        } else {
                            o
                        }
                    }
                    if (selectedFilter == "unpaid") {
                        list = list.filter { it.paymentStatus.equals("unpaid", ignoreCase = true) && !it.status.equals("delivered", ignoreCase = true) }
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
                    if (isSearchActive) {
                        OutlinedTextField(
                            value = searchQuery,
                            onValueChange = {
                                searchQuery = it
                                loadOrders()
                            },
                            placeholder = { Text(com.dinepos.app.core.localization.L10n.searchOrders(isHi), fontSize = 14.sp) },
                            singleLine = true,
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = BrandOrange,
                                unfocusedBorderColor = Color.Transparent,
                                focusedContainerColor = Color.White,
                                unfocusedContainerColor = Color.White
                            ),
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(50.dp)
                        )
                    } else {
                        Text(
                            text = com.dinepos.app.core.localization.L10n.grindingOrdersTitle(isHi),
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    }
                },
                navigationIcon = {
                    IconButton(onClick = {
                        if (isSearchActive) {
                            isSearchActive = false
                            if (searchQuery.isNotBlank()) {
                                searchQuery = ""
                                loadOrders()
                            }
                        } else {
                            onNavigateBack()
                        }
                    }) {
                        Icon(
                            imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                            contentDescription = "Back",
                            tint = BrandDark
                        )
                    }
                },
                actions = {
                    if (isSearchActive) {
                        IconButton(onClick = {
                            if (searchQuery.isNotEmpty()) {
                                searchQuery = ""
                                loadOrders()
                            } else {
                                isSearchActive = false
                            }
                        }) {
                            Icon(Icons.Default.Close, contentDescription = "Close Search", tint = BrandDark)
                        }
                    } else {
                        IconButton(onClick = { isSearchActive = true }) {
                            Icon(Icons.Default.Search, contentDescription = "Search", tint = BrandDark)
                        }
                        IconButton(onClick = { loadOrders() }) {
                            Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = BrandDark)
                        }
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
                            com.dinepos.app.core.localization.L10n.allDates(isHi)
                        } else if (selectedDate == todayStr) {
                            if (isHi) "आज (${displayDateFormat.format(Date())})" else "Today (${displayDateFormat.format(Date())})"
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

            // Combined Single-Row Dropdown Filters
            // Left: Date filter (Today, Yesterday, All Dates)
            // Right: Status filter (All Status, Received, In Process, Ready, Delivered, Unpaid)
            var dateMenuExpanded by remember { mutableStateOf(false) }
            var statusMenuExpanded by remember { mutableStateOf(false) }

            val calYesterday = Calendar.getInstance().apply { add(Calendar.DAY_OF_MONTH, -1) }
            val yesterdayStr = remember { isoDateFormat.format(calYesterday.time) }

            val dateDropdownLabel = when {
                selectedDate == null -> com.dinepos.app.core.localization.L10n.allDates(isHi)
                selectedDate == todayStr -> com.dinepos.app.core.localization.L10n.today(isHi)
                selectedDate == yesterdayStr -> com.dinepos.app.core.localization.L10n.yesterday(isHi)
                else -> {
                    try {
                        isoDateFormat.parse(selectedDate!!)?.let { displayDateFormat.format(it) } ?: selectedDate!!
                    } catch (e: Exception) {
                        selectedDate!!
                    }
                }
            }

            val statusDropdownLabel = filterOptions.firstOrNull { it.first == selectedFilter }?.second ?: com.dinepos.app.core.localization.L10n.allStatus(isHi)

            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp, vertical = 6.dp),
                horizontalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                // LEFT DROPDOWN: Date (Today, Yesterday, All Dates)
                Box(modifier = Modifier.weight(1f)) {
                    Surface(
                        onClick = { dateMenuExpanded = true },
                        shape = RoundedCornerShape(12.dp),
                        color = BrandSurface,
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(44.dp)
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxSize()
                                .padding(horizontal = 10.dp),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Row(
                                verticalAlignment = Alignment.CenterVertically,
                                modifier = Modifier.weight(1f, fill = false)
                            ) {
                                Icon(
                                    imageVector = Icons.Default.CalendarToday,
                                    contentDescription = null,
                                    tint = BrandOrange,
                                    modifier = Modifier.size(16.dp)
                                )
                                Spacer(modifier = Modifier.width(6.dp))
                                Text(
                                    text = dateDropdownLabel,
                                    fontSize = 12.5.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = BrandDark,
                                    maxLines = 1
                                )
                            }
                            Icon(
                                imageVector = Icons.Default.ArrowDropDown,
                                contentDescription = null,
                                tint = TextSecondary,
                                modifier = Modifier.size(20.dp)
                            )
                        }
                    }

                    DropdownMenu(
                        expanded = dateMenuExpanded,
                        onDismissRequest = { dateMenuExpanded = false }
                    ) {
                        DropdownMenuItem(
                            text = {
                                Text(
                                    text = com.dinepos.app.core.localization.L10n.today(isHi),
                                    fontWeight = if (selectedDate == todayStr) FontWeight.Bold else FontWeight.Normal,
                                    color = if (selectedDate == todayStr) BrandOrange else BrandDark
                                )
                            },
                            onClick = {
                                selectedDate = todayStr
                                dateMenuExpanded = false
                            }
                        )
                        DropdownMenuItem(
                            text = {
                                Text(
                                    text = com.dinepos.app.core.localization.L10n.yesterday(isHi),
                                    fontWeight = if (selectedDate == yesterdayStr) FontWeight.Bold else FontWeight.Normal,
                                    color = if (selectedDate == yesterdayStr) BrandOrange else BrandDark
                                )
                            },
                            onClick = {
                                selectedDate = yesterdayStr
                                dateMenuExpanded = false
                            }
                        )
                        DropdownMenuItem(
                            text = {
                                Text(
                                    text = com.dinepos.app.core.localization.L10n.allDates(isHi),
                                    fontWeight = if (selectedDate == null) FontWeight.Bold else FontWeight.Normal,
                                    color = if (selectedDate == null) BrandOrange else BrandDark
                                )
                            },
                            onClick = {
                                selectedDate = null
                                dateMenuExpanded = false
                            }
                        )
                        HorizontalDivider()
                        DropdownMenuItem(
                            text = {
                                Text(
                                    text = com.dinepos.app.core.localization.L10n.customDate(isHi),
                                    color = BrandOrange,
                                    fontWeight = FontWeight.Bold
                                )
                            },
                            onClick = {
                                dateMenuExpanded = false
                                showDatePicker()
                            }
                        )
                    }
                }

                // RIGHT DROPDOWN: Status (All, Received, In Process, Ready, Delivered, Unpaid)
                Box(modifier = Modifier.weight(1f)) {
                    Surface(
                        onClick = { statusMenuExpanded = true },
                        shape = RoundedCornerShape(12.dp),
                        color = BrandSurface,
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(44.dp)
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxSize()
                                .padding(horizontal = 10.dp),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Row(
                                verticalAlignment = Alignment.CenterVertically,
                                modifier = Modifier.weight(1f, fill = false)
                            ) {
                                Icon(
                                    imageVector = Icons.Default.FilterList,
                                    contentDescription = null,
                                    tint = Color(0xFF3B82F6),
                                    modifier = Modifier.size(16.dp)
                                )
                                Spacer(modifier = Modifier.width(6.dp))
                                Text(
                                    text = statusDropdownLabel,
                                    fontSize = 12.5.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = BrandDark,
                                    maxLines = 1
                                )
                            }
                            Icon(
                                imageVector = Icons.Default.ArrowDropDown,
                                contentDescription = null,
                                tint = TextSecondary,
                                modifier = Modifier.size(20.dp)
                            )
                        }
                    }

                    DropdownMenu(
                        expanded = statusMenuExpanded,
                        onDismissRequest = { statusMenuExpanded = false }
                    ) {
                        filterOptions.forEach { (key, label) ->
                            DropdownMenuItem(
                                text = {
                                    Text(
                                        text = label,
                                        fontWeight = if (selectedFilter == key) FontWeight.Bold else FontWeight.Normal,
                                        color = if (selectedFilter == key) BrandOrange else BrandDark
                                    )
                                },
                                onClick = {
                                    selectedFilter = key
                                    statusMenuExpanded = false
                                }
                            )
                        }
                    }
                }
            }

            val activeOrders = remember(orders, hiddenVersion) {
                orders.filter { !sessionManager.isOrderHidden(it.id) }
            }
            val totalRevenue = remember(activeOrders) {
                activeOrders.sumOf { it.totalAmount }
            }
            val hiddenCount = orders.size - activeOrders.size

            if (orders.isNotEmpty()) {
                Card(
                    shape = RoundedCornerShape(12.dp),
                    colors = CardDefaults.cardColors(containerColor = BrandDark),
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 4.dp)
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(horizontal = 14.dp, vertical = 8.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column {
                            Text(
                                text = if (isHi) "कुल सक्रिय ऑर्डर्स" else "ACTIVE ORDERS",
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                color = TextMuted
                            )
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text(
                                    text = "${activeOrders.size}",
                                    fontSize = 17.sp,
                                    fontWeight = FontWeight.Black,
                                    color = Color.White
                                )
                                if (hiddenCount > 0) {
                                    Spacer(modifier = Modifier.width(6.dp))
                                    Text(
                                        text = "(${hiddenCount} ${if (isHi) "छिपे" else "hidden"})",
                                        fontSize = 11.sp,
                                        color = TextMuted
                                    )
                                }
                            }
                        }

                        Column(horizontalAlignment = Alignment.End) {
                            Text(
                                text = if (isHi) "कुल राशि" else "TOTAL AMOUNT",
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                color = TextMuted
                            )
                            Text(
                                text = CurrencyFormatter.formatInr(totalRevenue),
                                fontSize = 17.sp,
                                fontWeight = FontWeight.Black,
                                color = BrandOrange
                            )
                        }
                    }
                }
            }

            if (isLoading) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = BrandOrange)
                }
            } else if (orders.isEmpty()) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text(
                        text = if (isHi) "कोई पिसाई ऑर्डर नहीं मिला।" else "No grinding orders found.",
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
                        val isOrderHidden = remember(hiddenVersion, order.id) {
                            sessionManager.isOrderHidden(order.id)
                        }
                        MillOrderCard(
                            order = order,
                            restaurantName = restaurantName,
                            isHidden = isOrderHidden,
                            onLongPress = {
                                actionMenuOrder = order
                            },
                            onUpdateStatus = { newStatus ->
                                scope.launch {
                                    val res = millRepository.updateOrderStatus(order.id, newStatus)
                                    if (res is Resource.Success) {
                                        if (newStatus.equals("delivered", ignoreCase = true)) {
                                            millRepository.updateOrderPayment(order.id, "paid", "cash")
                                        }
                                        val updatedOrder = order.copy(
                                            status = newStatus,
                                            paymentStatus = if (newStatus.equals("delivered", ignoreCase = true)) "paid" else order.paymentStatus
                                        )
                                        orders = orders.map { if (it.id == order.id) updatedOrder else it }
                                        // Show WhatsApp / notification modal as requested!
                                        notifyModalOrder = updatedOrder to newStatus
                                    } else if (res is Resource.Error) {
                                        Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
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

        // 1. WhatsApp / Order Ready / Order Delivered Notification Modal
        notifyModalOrder?.let { (modalOrder, action) ->
            val isReadyAction = action.equals("ready", ignoreCase = true)
            val cleanPhone = modalOrder.customerPhone.filter { it.isDigit() }
            val hasPhone = cleanPhone.length >= 10

            AlertDialog(
                onDismissRequest = { notifyModalOrder = null },
                title = {
                    Text(
                        text = if (isReadyAction) {
                            if (isHi) "ऑर्डर तैयार है" else "Order Ready"
                        } else {
                            if (isHi) "ऑर्डर डिलीवर हो गया" else "Order Delivered"
                        },
                        fontWeight = FontWeight.Bold,
                        fontSize = 18.sp,
                        color = BrandDark
                    )
                },
                text = {
                    Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        Text(
                            text = "${if (isHi) "ऑर्डर" else "Order"} #${modalOrder.orderNumber} • ${modalOrder.customerName.ifBlank { if (isHi) "सामान्य ग्राहक" else "Walk-in Customer" }}",
                            fontWeight = FontWeight.Bold,
                            fontSize = 15.sp,
                            color = BrandDark
                        )
                        val locSvc = L10n.localizeService(modalOrder.serviceName, isHi)
                        val weightUnit = if (isHi) "किलो" else "KG"
                        Text(
                            text = "$locSvc (${modalOrder.weightKg} $weightUnit)",
                            fontSize = 14.sp,
                            color = TextSecondary
                        )
                        Text(
                            text = "${if (isHi) "कुल राशि" else "Total Amount"}: ${CurrencyFormatter.formatInr(modalOrder.totalAmount)}",
                            fontWeight = FontWeight.Bold,
                            fontSize = 15.sp,
                            color = BrandOrange
                        )
                        if (hasPhone) {
                            Spacer(modifier = Modifier.height(4.dp))
                            Text(
                                text = if (isReadyAction) {
                                    if (isHi) "ग्राहक को WhatsApp पर सूचित करें कि पिसाई तैयार है और लेने आ जाएं।"
                                    else "Notify customer on WhatsApp that grinding is ready to pick up."
                                } else {
                                    if (isHi) "ग्राहक को WhatsApp पर सूचित करें कि ऑर्डर डिलीवर हो चुका है।"
                                    else "Notify customer on WhatsApp that order has been delivered."
                                },
                                fontSize = 12.sp,
                                color = TextSecondary
                            )
                        }
                    }
                },
                confirmButton = {
                    if (hasPhone) {
                        Button(
                            onClick = {
                                val locSvc = L10n.localizeService(modalOrder.serviceName, isHi)
                                val msg = if (isReadyAction) {
                                    if (isHi) "नमस्ते ${modalOrder.customerName} जी, आपकी $locSvc तैयार हो चुकी है, कृपया आकर ले जाएं। कुल राशि ₹${modalOrder.totalAmount} हुई है। - $restaurantName"
                                    else "Dear ${modalOrder.customerName}, your order for $locSvc (${modalOrder.weightKg} KG) is ready for pickup. Total: ₹${modalOrder.totalAmount}. - $restaurantName"
                                } else {
                                    if (isHi) "नमस्ते ${modalOrder.customerName} जी, आपका ऑर्डर #${modalOrder.orderNumber} ($locSvc) डिलीवर हो चुका है। कुल राशि ₹${modalOrder.totalAmount} का भुगतान प्राप्त हुआ। धन्यवाद! - $restaurantName"
                                    else "Dear ${modalOrder.customerName}, your order #${modalOrder.orderNumber} ($locSvc) has been delivered. Total: ₹${modalOrder.totalAmount}. Thank you! - $restaurantName"
                                }
                                WhatsAppHelper.openChat(context, modalOrder.customerPhone, msg)
                                notifyModalOrder = null
                            },
                            colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF25D366)),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.Chat,
                                contentDescription = "WhatsApp",
                                tint = Color.White,
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = if (isHi) "WhatsApp पर सूचित करें" else "Notify on WhatsApp",
                                color = Color.White,
                                fontWeight = FontWeight.Bold,
                                fontSize = 13.sp
                            )
                        }
                    }
                },
                dismissButton = {
                    TextButton(onClick = { notifyModalOrder = null }) {
                        Text(
                            text = if (isHi) "बंद करें" else "Close",
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    }
                }
            )
        }

        // 2. Action Menu Dialog on Holding (Long-Press) Order Card
        actionMenuOrder?.let { selOrder ->
            val isOrderCurrentlyHidden = sessionManager.isOrderHidden(selOrder.id)
            AlertDialog(
                onDismissRequest = { actionMenuOrder = null },
                title = {
                    Text(
                        text = "Order #${selOrder.orderNumber}",
                        fontWeight = FontWeight.Bold,
                        fontSize = 18.sp,
                        color = BrandDark
                    )
                },
                text = {
                    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                        Text(
                            text = "${selOrder.serviceName} • ${selOrder.weightKg} KG • ${CurrencyFormatter.formatInr(selOrder.totalAmount)}",
                            fontSize = 13.sp,
                            color = TextSecondary
                        )
                        HorizontalDivider(color = BrandBorder)

                        // Hide / Unhide option
                        Card(
                            onClick = {
                                val nowHidden = sessionManager.toggleOrderHidden(selOrder.id)
                                hiddenVersion++
                                actionMenuOrder = null
                                Toast.makeText(
                                    context,
                                    if (nowHidden) {
                                        if (isHi) "ऑर्डर #${selOrder.orderNumber} छिपा दिया गया (कुल में नहीं जुड़ेगा)" else "Order #${selOrder.orderNumber} hidden (excluded from totals)"
                                    } else {
                                        if (isHi) "ऑर्डर #${selOrder.orderNumber} वापस सक्रिय कर दिया गया" else "Order #${selOrder.orderNumber} unhidden"
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
                                actionMenuOrder = null
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
                                        text = if (isHi) "सिस्टम से हमेशा के लिए हटा दें" else "Permanently remove from records",
                                        fontSize = 11.sp,
                                        color = StatusError.copy(alpha = 0.8f)
                                    )
                                }
                            }
                        }
                    }
                },
                confirmButton = {},
                dismissButton = {
                    TextButton(onClick = { actionMenuOrder = null }) {
                        Text(if (isHi) "रद्द करें" else "Cancel", fontWeight = FontWeight.Bold, color = BrandDark)
                    }
                }
            )
        }

        // 3. Delete Confirm Dialog
        deleteConfirmOrder?.let { toDelete ->
            AlertDialog(
                onDismissRequest = { deleteConfirmOrder = null },
                title = {
                    Text(
                        text = if (isHi) "ऑर्डर हटाएं?" else "Delete Order?",
                        fontWeight = FontWeight.Bold,
                        color = StatusError
                    )
                },
                text = {
                    Text(
                        text = if (isHi) {
                            "क्या आप वाकई ऑर्डर #${toDelete.orderNumber} को हटाना चाहते हैं? यह पूरी तरह हटा दिया जाएगा।"
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
                                millRepository.deleteOrder(toDelete.id)
                            }
                            orders = orders.filter { it.id != toDelete.id }
                            deleteConfirmOrder = null
                            Toast.makeText(
                                context,
                                if (isHi) "ऑर्डर #${toDelete.orderNumber} हटा दिया गया" else "Order #${toDelete.orderNumber} deleted",
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
}

@OptIn(ExperimentalFoundationApi::class)
@Composable
private fun MillOrderCard(
    order: MillOrderDto,
    restaurantName: String,
    isHidden: Boolean,
    onLongPress: () -> Unit,
    onUpdateStatus: (String) -> Unit,
    onMarkPaid: () -> Unit
) {
    val context = LocalContext.current
    val isDelivered = order.status.equals("delivered", ignoreCase = true)
    val isPaid = order.paymentStatus.equals("paid", ignoreCase = true) || isDelivered
    val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"

    val statusColor = when (order.status.lowercase()) {
        "received" -> Color(0xFF3B82F6)
        "processing" -> BrandAmber
        "ready" -> BrandEmerald
        "delivered" -> BrandDark
        else -> TextMuted
    }

    val statusBadgeText = when (order.status.lowercase()) {
        "received" -> com.dinepos.app.core.localization.L10n.received(isHi)
        "processing" -> com.dinepos.app.core.localization.L10n.inProcess(isHi)
        "ready" -> com.dinepos.app.core.localization.L10n.ready(isHi)
        "delivered" -> com.dinepos.app.core.localization.L10n.delivered(isHi)
        else -> order.status.uppercase()
    }

    val nextStatus = when (order.status.lowercase()) {
        "received", "processing" -> "ready" to com.dinepos.app.core.localization.L10n.markReady(isHi)
        "ready" -> "delivered" to com.dinepos.app.core.localization.L10n.markDelivered(isHi)
        else -> null
    }

    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(
            containerColor = if (isHidden) BrandSurface.copy(alpha = 0.6f) else BrandSurface
        ),
        border = BorderStroke(1.dp, if (isHidden) Color.LightGray else BrandBorder),
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(16.dp))
            .combinedClickable(
                onClick = {},
                onLongClick = onLongPress
            )
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            // Top Row: Daily Order # & Status Badge
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Text(
                            text = if (isHi) "ऑर्डर #${order.orderNumber}" else "Order #${order.orderNumber}",
                            fontWeight = FontWeight.Black,
                            fontSize = 18.sp,
                            color = if (isHidden) TextMuted else BrandDark
                        )
                        if (isHidden) {
                            Surface(
                                shape = RoundedCornerShape(6.dp),
                                color = Color.LightGray.copy(alpha = 0.4f)
                            ) {
                                Text(
                                    text = if (isHi) "छिपा हुआ" else "HIDDEN",
                                    fontSize = 10.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = TextSecondary,
                                    modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                                )
                            }
                        }
                    }
                    Text(
                        text = "ID: #${order.id} • ${order.orderDate}${order.orderTime?.let { " $it" } ?: ""}",
                        fontSize = 11.sp,
                        color = TextSecondary
                    )
                }

                Surface(
                    shape = RoundedCornerShape(6.dp),
                    color = statusColor.copy(alpha = if (isHidden) 0.05f else 0.12f),
                    border = BorderStroke(1.dp, statusColor.copy(alpha = if (isHidden) 0.2f else 0.4f))
                ) {
                    Text(
                        text = statusBadgeText.uppercase(),
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = if (isHidden) TextMuted else statusColor,
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
                color = if (isHidden) TextMuted else BrandDark
            )

            // Service & Weight
            val locServiceName = L10n.localizeService(order.serviceName, isHi)
            val weightUnit = if (isHi) "किलो" else "KG"
            val rateUnit = if (isHi) "प्रति किलो" else "KG"
            Text(
                text = if (isHi) "$locServiceName • ${order.weightKg} $weightUnit @ ₹${order.ratePerKg}/$rateUnit"
                else "${order.serviceName} • ${order.weightKg} KG @ Rs.${order.ratePerKg}/KG",
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
                        color = if (isHidden) TextMuted else BrandDark
                    )
                    Text(
                        text = if (isPaid) (if (isHi) "भुगतान पूर्ण" else "Paid via ${order.paymentMethod.uppercase()}") else com.dinepos.app.core.localization.L10n.pendingDue(isHi),
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        color = if (isPaid) BrandEmerald else BrandAmber
                    )
                }

                if (isHidden) {
                    Text(
                        text = if (isHi) "दबाकर रखें अनहाइड करने के लिए" else "Hold to unhide",
                        fontSize = 12.sp,
                        color = TextMuted
                    )
                } else {
                    // Actions Row: WhatsApp & Next Action
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        // WhatsApp Button (Only if phone entered)
                        if (order.customerPhone.isNotBlank()) {
                            IconButton(
                                onClick = {
                                    val locSvc = L10n.localizeService(order.serviceName, isHi)
                                    val msg = if (isDelivered) {
                                        if (isHi) "नमस्ते ${order.customerName} जी, आपका ऑर्डर #${order.orderNumber} ($locSvc) डिलीवर हो चुका है। ₹${order.totalAmount} का भुगतान प्राप्त हुआ। धन्यवाद! - $restaurantName"
                                        else "Dear ${order.customerName}, your order #${order.orderNumber} ($locSvc) has been delivered. Total ₹${order.totalAmount} received. Thank you! - $restaurantName"
                                    } else if (order.status.equals("ready", ignoreCase = true)) {
                                        if (isHi) "नमस्ते ${order.customerName} जी, आपकी $locSvc तैयार हो चुकी है, कृपया आकर ले जाएं। कुल राशि ₹${order.totalAmount} हुई है। - $restaurantName"
                                        else "Dear ${order.customerName}, your $locSvc is ready, please come and collect. Total ₹${order.totalAmount}. - $restaurantName"
                                    } else {
                                        if (isHi) "नमस्ते ${order.customerName} जी, आपका ऑर्डर #${order.orderNumber} ($locSvc, ${order.weightKg} $weightUnit) प्राप्त हुआ है। कुल राशि ₹${order.totalAmount}। - $restaurantName"
                                        else "Dear ${order.customerName}, your order #${order.orderNumber} ($locSvc, ${order.weightKg} KG) has been received. Total ₹${order.totalAmount}. - $restaurantName"
                                    }
                                    WhatsAppHelper.openChat(context, order.customerPhone, msg)
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
                                Text(com.dinepos.app.core.localization.L10n.markPaid(isHi), fontSize = 12.sp, color = BrandEmerald)
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
}
