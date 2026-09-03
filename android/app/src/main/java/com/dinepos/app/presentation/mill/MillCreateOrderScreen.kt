package com.dinepos.app.presentation.mill

import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.CreateMillOrderRequestDto
import com.dinepos.app.data.dto.MillOrderDto
import com.dinepos.app.data.dto.MillServiceDto
import kotlinx.coroutines.launch
import java.net.URLEncoder

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MillCreateOrderScreen(
    onOrderCreated: () -> Unit,
    onNavigateBack: () -> Unit
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val millRepository = DinePosApp.instance.millRepository
    val restaurantName = DinePosApp.instance.sessionManager.getRestaurantName().ifBlank { "Atta Mill" }

    val defaultServices = remember {
        listOf(
            MillServiceDto(id = 1, name = "Wheat grinding", nameHi = "गेहूं पिसाई", ratePerKg = 5.0, active = 1),
            MillServiceDto(id = 2, name = "Daliya", nameHi = "दलिया", ratePerKg = 8.0, active = 1),
            MillServiceDto(id = 3, name = "Oil extraction", nameHi = "तेल पिराई", ratePerKg = 25.0, active = 1),
            MillServiceDto(id = 4, name = "Rice grinding", nameHi = "चावल पिसाई", ratePerKg = 7.0, active = 1),
            MillServiceDto(id = 5, name = "Other custom services", nameHi = "अन्य सेवा", ratePerKg = 10.0, active = 1)
        )
    }

    var services by remember { mutableStateOf<List<MillServiceDto>>(defaultServices) }
    var selectedService by remember { mutableStateOf<MillServiceDto?>(defaultServices[0]) }
    var serviceDropdownExpanded by remember { mutableStateOf(false) }

    var customerPhone by remember { mutableStateOf("") }
    var customerName by remember { mutableStateOf("") }
    var weightKgText by remember { mutableStateOf("") }
    var paymentStatus by remember { mutableStateOf("unpaid") }
    var paymentMethod by remember { mutableStateOf("cash") }
    var notes by remember { mutableStateOf("") }
    var isSubmitting by remember { mutableStateOf(false) }

    var createdOrderSuccess by remember { mutableStateOf<MillOrderDto?>(null) }

    LaunchedEffect(Unit) {
        when (val res = millRepository.getServices()) {
            is Resource.Success -> {
                val list = res.data?.filter { it.active == 1 } ?: emptyList()
                if (list.isNotEmpty()) {
                    services = list
                    selectedService = list[0]
                }
            }
            else -> {}
        }
    }

    val weightKg = weightKgText.toDoubleOrNull() ?: 0.0
    val ratePerKg = selectedService?.ratePerKg ?: 0.0
    val totalAmount = weightKg * ratePerKg

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "New Grinding Order",
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
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandBackground)
            )
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(horizontal = 16.dp)
                .verticalScroll(rememberScrollState()),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Customer Phone (Optional)
            OutlinedTextField(
                value = customerPhone,
                onValueChange = { input ->
                    customerPhone = input
                    if (input.length >= 10) {
                        scope.launch {
                            val res = millRepository.getCustomers(input)
                            if (res is Resource.Success && !res.data.isNullOrEmpty()) {
                                customerName = res.data[0].name
                            }
                        }
                    }
                },
                label = { Text("Customer Mobile / WhatsApp (Optional)") },
                placeholder = { Text("e.g. 9876543210") },
                singleLine = true,
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
                modifier = Modifier.fillMaxWidth()
            )

            // Customer Name (Optional)
            OutlinedTextField(
                value = customerName,
                onValueChange = { customerName = it },
                label = { Text("Customer Name (Optional)") },
                placeholder = { Text("Leave blank for Walk-in") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth()
            )

            // Service Selection Dropdown
            Text(
                text = "SELECT SERVICE *",
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold,
                color = TextSecondary,
                letterSpacing = 0.5.sp
            )

            ExposedDropdownMenuBox(
                expanded = serviceDropdownExpanded,
                onExpandedChange = { serviceDropdownExpanded = !serviceDropdownExpanded },
                modifier = Modifier.fillMaxWidth()
            ) {
                OutlinedTextField(
                    value = selectedService?.let {
                        if (!it.nameHi.isNullOrBlank()) "${it.name} (${it.nameHi}) - ${CurrencyFormatter.formatInr(it.ratePerKg)}/KG"
                        else "${it.name} - ${CurrencyFormatter.formatInr(it.ratePerKg)}/KG"
                    } ?: "Choose Grinding Service",
                    onValueChange = {},
                    readOnly = true,
                    label = { Text("Grinding Service *") },
                    trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = serviceDropdownExpanded) },
                    modifier = Modifier
                        .menuAnchor()
                        .fillMaxWidth()
                )

                ExposedDropdownMenu(
                    expanded = serviceDropdownExpanded,
                    onDismissRequest = { serviceDropdownExpanded = false }
                ) {
                    services.forEach { s ->
                        DropdownMenuItem(
                            text = {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Column {
                                        Text(
                                            text = s.name,
                                            fontWeight = FontWeight.SemiBold,
                                            fontSize = 15.sp,
                                            color = BrandDark
                                        )
                                        if (!s.nameHi.isNullOrBlank()) {
                                            Text(
                                                text = s.nameHi,
                                                fontSize = 12.sp,
                                                color = TextSecondary
                                            )
                                        }
                                    }
                                    Text(
                                        text = "${CurrencyFormatter.formatInr(s.ratePerKg)} / KG",
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 14.sp,
                                        color = BrandOrange
                                    )
                                }
                            },
                            onClick = {
                                selectedService = s
                                serviceDropdownExpanded = false
                            },
                            contentPadding = ExposedDropdownMenuDefaults.ItemContentPadding
                        )
                    }
                }
            }

            // Weight in KG (Full Width, Rate is taken automatically from selected service)
            OutlinedTextField(
                value = weightKgText,
                onValueChange = { weightKgText = it },
                label = { Text("Weight (KG) *") },
                placeholder = { Text("e.g. 10.0") },
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                modifier = Modifier.fillMaxWidth(),
                singleLine = true
            )

            // Dynamic Total Amount Banner
            Card(
                shape = RoundedCornerShape(14.dp),
                colors = CardDefaults.cardColors(containerColor = BrandDark),
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
                        Text(
                            text = "TOTAL AMOUNT",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = TextMuted
                        )
                        Text(
                            text = "${weightKg} KG x Rs.${ratePerKg}/KG (${selectedService?.name ?: ""})",
                            fontSize = 12.sp,
                            color = Color.White.copy(alpha = 0.7f)
                        )
                    }
                    Text(
                        text = CurrencyFormatter.formatInr(totalAmount),
                        fontSize = 24.sp,
                        fontWeight = FontWeight.Black,
                        color = Color.White
                    )
                }
            }

            // Payment Status Selection
            Text(
                text = "PAYMENT STATUS *",
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold,
                color = TextSecondary,
                letterSpacing = 0.5.sp
            )

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                listOf("unpaid" to "Unpaid (Due)", "paid" to "Paid").forEach { (st, label) ->
                    val isSel = paymentStatus == st
                    val activeColor = if (st == "paid") BrandEmerald else BrandAmber
                    Card(
                        onClick = { paymentStatus = st },
                        shape = RoundedCornerShape(12.dp),
                        colors = CardDefaults.cardColors(
                            containerColor = if (isSel) activeColor.copy(alpha = 0.15f) else BrandSurface
                        ),
                        border = BorderStroke(1.dp, if (isSel) activeColor else BrandBorder),
                        modifier = Modifier.weight(1f)
                    ) {
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 12.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                text = label,
                                fontWeight = FontWeight.Bold,
                                color = if (isSel) activeColor else BrandDark,
                                fontSize = 14.sp
                            )
                        }
                    }
                }
            }

            // Payment Mode (Cash / UPI)
            if (paymentStatus == "paid") {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    listOf("cash" to "Cash", "online" to "UPI / Online").forEach { (mode, label) ->
                        val isSel = paymentMethod == mode
                        Card(
                            onClick = { paymentMethod = mode },
                            shape = RoundedCornerShape(12.dp),
                            colors = CardDefaults.cardColors(
                                containerColor = if (isSel) BrandOrange.copy(alpha = 0.15f) else BrandSurface
                            ),
                            border = BorderStroke(1.dp, if (isSel) BrandOrange else BrandBorder),
                            modifier = Modifier.weight(1f)
                        ) {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 10.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                Text(
                                    text = label,
                                    fontWeight = FontWeight.SemiBold,
                                    color = if (isSel) BrandOrange else BrandDark,
                                    fontSize = 13.sp
                                )
                            }
                        }
                    }
                }
            }

            // Notes
            OutlinedTextField(
                value = notes,
                onValueChange = { notes = it },
                label = { Text("Notes (Optional)") },
                placeholder = { Text("e.g. Fine grinding, pack in 5kg bags") },
                maxLines = 2,
                modifier = Modifier.fillMaxWidth()
            )

            // Submit Button
            Button(
                onClick = {
                    if (weightKg <= 0) {
                        Toast.makeText(context, "Please enter valid weight in KG", Toast.LENGTH_SHORT).show()
                        return@Button
                    }
                    val finalCustomerName = customerName.trim().ifBlank { "Walk-in Customer" }
                    val finalCustomerPhone = customerPhone.trim()

                    isSubmitting = true
                    scope.launch {
                        val req = CreateMillOrderRequestDto(
                            customerName = finalCustomerName,
                            customerPhone = finalCustomerPhone,
                            serviceId = selectedService?.id,
                            serviceName = selectedService?.name ?: "Grinding",
                            weightKg = weightKg,
                            ratePerKg = ratePerKg,
                            paymentStatus = paymentStatus,
                            paymentMethod = paymentMethod,
                            notes = notes.ifBlank { null }
                        )
                        when (val res = millRepository.createOrder(req)) {
                            is Resource.Success -> {
                                isSubmitting = false
                                createdOrderSuccess = res.data
                            }
                            is Resource.Error -> {
                                Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
                                isSubmitting = false
                            }
                            else -> {
                                isSubmitting = false
                            }
                        }
                    }
                },
                enabled = !isSubmitting,
                shape = RoundedCornerShape(12.dp),
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
            ) {
                if (isSubmitting) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(24.dp))
                } else {
                    Text("Confirm & Save Order", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                }
            }

            Spacer(modifier = Modifier.height(24.dp))
        }

        // Daily Order Confirmation Dialog
        createdOrderSuccess?.let { order ->
            AlertDialog(
                onDismissRequest = {
                    createdOrderSuccess = null
                    onOrderCreated()
                },
                icon = {
                    Icon(
                        imageVector = Icons.Default.CheckCircle,
                        contentDescription = null,
                        tint = BrandEmerald,
                        modifier = Modifier.size(48.dp)
                    )
                },
                title = {
                    Text(
                        text = "Order #${order.orderNumber} Confirmed",
                        fontWeight = FontWeight.Black,
                        fontSize = 20.sp,
                        color = BrandDark
                    )
                },
                text = {
                    Column(
                        modifier = Modifier.fillMaxWidth(),
                        verticalArrangement = Arrangement.spacedBy(6.dp)
                    ) {
                        Text(
                            text = "Daily Order Number: #${order.orderNumber}",
                            fontWeight = FontWeight.Bold,
                            color = BrandOrange,
                            fontSize = 15.sp
                        )
                        Text(
                            text = "Customer: ${order.customerName}",
                            color = BrandDark,
                            fontSize = 14.sp
                        )
                        Text(
                            text = "Service: ${order.serviceName} • ${order.weightKg} KG",
                            color = TextSecondary,
                            fontSize = 13.sp
                        )
                        Text(
                            text = "Total Amount: ${CurrencyFormatter.formatInr(order.totalAmount)} (${order.paymentStatus.uppercase()})",
                            fontWeight = FontWeight.Bold,
                            color = BrandDark,
                            fontSize = 14.sp
                        )
                    }
                },
                confirmButton = {
                    Button(
                        onClick = {
                            createdOrderSuccess = null
                            onOrderCreated()
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                    ) {
                        Text("Done")
                    }
                },
                dismissButton = {
                    if (order.customerPhone.isNotBlank()) {
                        OutlinedButton(
                            onClick = {
                                val msg = "Namaste ${order.customerName} ji, your order #${order.orderNumber} (${order.serviceName}, ${order.weightKg} KG) has been placed at $restaurantName. Total: Rs.${order.totalAmount}."
                                val clean = order.customerPhone.replace(Regex("[^0-9]"), "")
                                val url = "https://api.whatsapp.com/send?phone=91$clean&text=${URLEncoder.encode(msg, "UTF-8")}"
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
                                contentDescription = null,
                                tint = Color(0xFF25D366),
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text("WhatsApp", color = Color(0xFF25D366))
                        }
                    }
                }
            )
        }
    }
}
