package com.dinepos.app.presentation.mill

import android.Manifest
import android.app.Activity
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.provider.ContactsContract
import android.widget.Toast
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Contacts
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Phone
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
import androidx.compose.ui.window.PopupProperties
import androidx.core.content.ContextCompat
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.localization.L10n
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.core.utils.WhatsAppHelper
import com.dinepos.app.data.dto.CreateMillOrderRequestDto
import com.dinepos.app.data.dto.MillCustomerDto
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

    val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"

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

    // Existing customer suggestions from database
    var cachedCustomers by remember { mutableStateOf<List<MillCustomerDto>>(emptyList()) }
    var nameFilteredCustomers by remember { mutableStateOf<List<MillCustomerDto>>(emptyList()) }
    var nameDropdownExpanded by remember { mutableStateOf(false) }
    var phoneFilteredCustomers by remember { mutableStateOf<List<MillCustomerDto>>(emptyList()) }
    var phoneDropdownExpanded by remember { mutableStateOf(false) }

    fun selectCustomer(c: MillCustomerDto) {
        customerName = c.name
        val digits = c.phone.filter { it.isDigit() }
        val cleanDigits = when {
            digits.length == 10 && digits[0] in '6'..'9' -> digits
            digits.length > 10 && digits.takeLast(10)[0] in '6'..'9' -> digits.takeLast(10)
            else -> digits.take(10)
        }
        if (cleanDigits.isNotEmpty()) {
            customerPhone = cleanDigits
        }
        nameDropdownExpanded = false
        phoneDropdownExpanded = false
    }

    // Contact Picker Launcher
    val contactPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.StartActivityForResult()
    ) { result ->
        if (result.resultCode == Activity.RESULT_OK) {
            val contactUri = result.data?.data ?: return@rememberLauncherForActivityResult
            try {
                context.contentResolver.query(
                    contactUri,
                    arrayOf(
                        ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME,
                        ContactsContract.CommonDataKinds.Phone.NUMBER
                    ),
                    null,
                    null,
                    null
                )?.use { cursor ->
                    if (cursor.moveToFirst()) {
                        val nameIdx = cursor.getColumnIndex(ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME)
                        val numIdx = cursor.getColumnIndex(ContactsContract.CommonDataKinds.Phone.NUMBER)
                        val rawName = if (nameIdx >= 0) cursor.getString(nameIdx) ?: "" else ""
                        val rawNumber = if (numIdx >= 0) cursor.getString(numIdx) ?: "" else ""

                        val digits = rawNumber.filter { it.isDigit() }
                        val cleanDigits = when {
                            digits.length == 10 && digits[0] in '6'..'9' -> digits
                            digits.length > 10 && digits.takeLast(10)[0] in '6'..'9' -> digits.takeLast(10)
                            else -> digits.take(10)
                        }

                        if (cleanDigits.isNotEmpty()) {
                            customerPhone = cleanDigits
                        }
                        if (rawName.isNotBlank()) {
                            customerName = rawName.trim()
                        }
                        nameDropdownExpanded = false
                        phoneDropdownExpanded = false
                    }
                }
            } catch (e: Exception) {
                Toast.makeText(context, "Failed to read contact: ${e.localizedMessage}", Toast.LENGTH_SHORT).show()
            }
        }
    }

    // Permission Launcher for READ_CONTACTS
    val contactPermissionLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        if (isGranted) {
            val intent = Intent(Intent.ACTION_PICK, ContactsContract.CommonDataKinds.Phone.CONTENT_URI)
            contactPickerLauncher.launch(intent)
        } else {
            Toast.makeText(
                context,
                if (isHi) "संपर्क चुनने के लिए अनुमति आवश्यक है" else "Permission needed to choose from contacts",
                Toast.LENGTH_SHORT
            ).show()
        }
    }

    fun openContactPicker() {
        val hasPermission = ContextCompat.checkSelfPermission(
            context,
            Manifest.permission.READ_CONTACTS
        ) == PackageManager.PERMISSION_GRANTED

        if (hasPermission) {
            val intent = Intent(Intent.ACTION_PICK, ContactsContract.CommonDataKinds.Phone.CONTENT_URI)
            contactPickerLauncher.launch(intent)
        } else {
            contactPermissionLauncher.launch(Manifest.permission.READ_CONTACTS)
        }
    }

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
        // Cache customers for instant 2-3 character auto-suggestions
        when (val res = millRepository.getCustomers()) {
            is Resource.Success -> {
                cachedCustomers = res.data ?: emptyList()
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
                        text = if (isHi) "नया पिसाई ऑर्डर" else "New Grinding Order",
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
            // Customer Phone with Contact Picker and Suggestions
            Box(modifier = Modifier.fillMaxWidth()) {
                OutlinedTextField(
                    value = customerPhone,
                    onValueChange = { input ->
                        val digits = input.filter { it.isDigit() }.take(10)
                        if (digits.isEmpty() || digits[0] in '6'..'9') {
                            customerPhone = digits
                            if (digits.length in 2..9) {
                                val matches = cachedCustomers.filter { it.phone.contains(digits) }
                                phoneFilteredCustomers = matches
                                phoneDropdownExpanded = matches.isNotEmpty()
                            } else {
                                phoneDropdownExpanded = false
                            }

                            if (digits.length == 10) {
                                scope.launch {
                                    val res = millRepository.getCustomers(digits)
                                    if (res is Resource.Success && !res.data.isNullOrEmpty()) {
                                        customerName = res.data[0].name
                                    }
                                }
                            }
                        } else {
                            Toast.makeText(context, if (isHi) "नंबर 6, 7, 8 या 9 से शुरू होना चाहिए" else "Number must start with 6, 7, 8, or 9", Toast.LENGTH_SHORT).show()
                        }
                    },
                    label = { Text(if (isHi) "ग्राहक मोबाइल / WhatsApp (वैकल्पिक)" else "Customer Mobile / WhatsApp (Optional)") },
                    placeholder = { Text(if (isHi) "उदा. 9876543210 (10 अंक)" else "e.g. 9876543210 (10 digits)") },
                    trailingIcon = {
                        IconButton(onClick = { openContactPicker() }) {
                            Icon(
                                imageVector = Icons.Default.Contacts,
                                contentDescription = if (isHi) "संपर्क चुनें" else "Choose from contacts",
                                tint = BrandOrange
                            )
                        }
                    },
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                    modifier = Modifier.fillMaxWidth()
                )

                DropdownMenu(
                    expanded = phoneDropdownExpanded,
                    onDismissRequest = { phoneDropdownExpanded = false },
                    properties = PopupProperties(focusable = false),
                    modifier = Modifier
                        .fillMaxWidth(0.9f)
                        .background(BrandSurface)
                ) {
                    phoneFilteredCustomers.take(5).forEach { c ->
                        DropdownMenuItem(
                            text = {
                                Column {
                                    Text(
                                        text = c.name,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 14.sp,
                                        color = BrandDark
                                    )
                                    Text(
                                        text = c.phone,
                                        fontSize = 12.sp,
                                        color = TextSecondary
                                    )
                                }
                            },
                            onClick = {
                                selectCustomer(c)
                            }
                        )
                    }
                }
            }

            // Customer Name with Auto-suggestions (2-3 initials / letters)
            Box(modifier = Modifier.fillMaxWidth()) {
                OutlinedTextField(
                    value = customerName,
                    onValueChange = { input ->
                        customerName = input
                        val q = input.trim()
                        if (q.length >= 2) {
                            val localMatches = cachedCustomers.filter { c ->
                                c.name.contains(q, ignoreCase = true) || c.phone.contains(q)
                            }
                            nameFilteredCustomers = localMatches
                            nameDropdownExpanded = localMatches.isNotEmpty()

                            // Also query server in background for freshly registered customers
                            scope.launch {
                                val serverRes = millRepository.getCustomers(q)
                                if (serverRes is Resource.Success && !serverRes.data.isNullOrEmpty()) {
                                    val merged = (nameFilteredCustomers + serverRes.data).distinctBy { it.id }
                                    nameFilteredCustomers = merged
                                    nameDropdownExpanded = merged.isNotEmpty()
                                }
                            }
                        } else {
                            nameDropdownExpanded = false
                        }
                    },
                    label = { Text(if (isHi) "ग्राहक का नाम (वैकल्पिक)" else "Customer Name (Optional)") },
                    placeholder = { Text(if (isHi) "उदा. रमेश कुमार (2-3 अक्षर लिखें)" else "e.g. Ramesh Kumar (type 2-3 letters)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )

                DropdownMenu(
                    expanded = nameDropdownExpanded,
                    onDismissRequest = { nameDropdownExpanded = false },
                    properties = PopupProperties(focusable = false),
                    modifier = Modifier
                        .fillMaxWidth(0.9f)
                        .background(BrandSurface)
                ) {
                    nameFilteredCustomers.take(5).forEach { c ->
                        DropdownMenuItem(
                            text = {
                                Column {
                                    Text(
                                        text = c.name,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 14.sp,
                                        color = BrandDark
                                    )
                                    Text(
                                        text = c.phone,
                                        fontSize = 12.sp,
                                        color = TextSecondary
                                    )
                                }
                            },
                            onClick = {
                                selectCustomer(c)
                            }
                        )
                    }
                }
            }

            // Service Selection Dropdown
            Text(
                text = if (isHi) "पिसाई सेवा चुनें *" else "SELECT SERVICE *",
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
                        val displayName = if (isHi && !it.nameHi.isNullOrBlank()) it.nameHi else it.name
                        val unit = if (isHi) "किलो" else "KG"
                        "$displayName - ${CurrencyFormatter.formatInr(it.ratePerKg)}/$unit"
                    } ?: (if (isHi) "पिसाई सेवा चुनें" else "Choose Grinding Service"),
                    onValueChange = {},
                    readOnly = true,
                    label = { Text(if (isHi) "पिसाई सेवा *" else "Grinding Service *") },
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
                        val primary = if (isHi && !s.nameHi.isNullOrBlank()) s.nameHi else s.name
                        val secondary = if (isHi && !s.nameHi.isNullOrBlank()) s.name else s.nameHi
                        val unit = if (isHi) "किलो" else "KG"
                        DropdownMenuItem(
                            text = {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Column {
                                        Text(
                                            text = primary,
                                            fontWeight = FontWeight.SemiBold,
                                            fontSize = 15.sp,
                                            color = BrandDark
                                        )
                                        if (!secondary.isNullOrBlank()) {
                                            Text(
                                                text = secondary,
                                                fontSize = 12.sp,
                                                color = TextSecondary
                                            )
                                        }
                                    }
                                    Text(
                                        text = "${CurrencyFormatter.formatInr(s.ratePerKg)} / $unit",
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

            // Weight in KG (Only numbers, max 100 KG)
            OutlinedTextField(
                value = weightKgText,
                onValueChange = { input ->
                    var dotCount = 0
                    val filtered = input.filter { char ->
                        if (char.isDigit()) true
                        else if (char == '.' && dotCount == 0) {
                            dotCount++
                            true
                        } else false
                    }
                    val num = filtered.toDoubleOrNull()
                    if (filtered.isEmpty()) {
                        weightKgText = ""
                    } else if (num != null && num <= 100.0) {
                        weightKgText = filtered
                    } else if (num != null && num > 100.0) {
                        weightKgText = "100"
                        Toast.makeText(context, if (isHi) "अधिकतम वजन 100 KG तक ही संभव है" else "Maximum weight allowed is 100 KG", Toast.LENGTH_SHORT).show()
                    }
                },
                label = { Text(if (isHi) "वजन (KG, अधिकतम 100) *" else "Weight (KG, Max 100) *") },
                placeholder = { Text("e.g. 10.0 (Max 100)") },
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
                            text = if (isHi) "कुल राशि" else "TOTAL AMOUNT",
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold,
                            color = TextMuted
                        )
                        val svcName = selectedService?.let { if (isHi && !it.nameHi.isNullOrBlank()) it.nameHi else it.name } ?: ""
                        val unit = if (isHi) "किलो" else "KG"
                        val rateUnit = if (isHi) "प्रति किलो" else "KG"
                        Text(
                            text = if (isHi) "$weightKg $unit x ₹$ratePerKg/$rateUnit ($svcName)"
                            else "$weightKg KG x Rs.$ratePerKg/KG ($svcName)",
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
                text = if (isHi) "भुगतान स्थिति *" else "PAYMENT STATUS *",
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold,
                color = TextSecondary,
                letterSpacing = 0.5.sp
            )

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                listOf(
                    "unpaid" to (if (isHi) "बकाया (Due)" else "Unpaid (Due)"),
                    "paid" to (if (isHi) "जमा (Paid)" else "Paid")
                ).forEach { (st, label) ->
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
                    listOf(
                        "cash" to (if (isHi) "नकद (Cash)" else "Cash"),
                        "online" to (if (isHi) "ऑनलाइन / UPI" else "UPI / Online")
                    ).forEach { (mode, label) ->
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
                label = { Text(if (isHi) "नोट्स (वैकल्पिक)" else "Notes (Optional)") },
                placeholder = { Text(if (isHi) "उदा. बारीक पिसाई, 5 किलो के बैग में पैक करें" else "e.g. Fine grinding, pack in 5kg bags") },
                maxLines = 2,
                modifier = Modifier.fillMaxWidth()
            )

            // Submit Button
            Button(
                onClick = {
                    val finalCustomerPhone = customerPhone.trim()
                    if (finalCustomerPhone.isNotEmpty()) {
                        if (finalCustomerPhone.length != 10 || finalCustomerPhone[0] !in '6'..'9') {
                            Toast.makeText(context, if (isHi) "मोबाइल नंबर 10 अंकों का और 6-9 से शुरू होना चाहिए" else "Mobile number must be 10 digits starting with 6-9", Toast.LENGTH_SHORT).show()
                            return@Button
                        }
                    }

                    if (weightKg <= 0.0 || weightKg > 100.0) {
                        Toast.makeText(context, if (isHi) "कृपया 0.1 से 100 KG के बीच वजन दर्ज करें" else "Please enter weight between 0.1 and 100 KG (Max 100 KG)", Toast.LENGTH_SHORT).show()
                        return@Button
                    }
                    val finalCustomerName = customerName.trim().ifBlank { if (isHi) "काउंटर ग्राहक" else "Walk-in Customer" }

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
                    Text(if (isHi) "ऑर्डर कन्फर्म करें" else "Confirm & Save Order", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                }
            }

            Spacer(modifier = Modifier.height(24.dp))
        }

        // Daily Order Confirmation Dialog
        createdOrderSuccess?.let { order ->
            val locService = L10n.localizeService(order.serviceName, isHi)
            val weightUnit = if (isHi) "किलो" else "KG"
            val paymentNote = if (order.paymentStatus.equals("paid", true)) {
                if (isHi) "भुगतान प्राप्त" else "PAID"
            } else {
                if (isHi) "भुगतान बाकी" else "PENDING"
            }

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
                        text = L10n.orderConfirmedTitle(order.orderNumber, isHi),
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
                            text = L10n.dailyOrderToken(order.orderNumber, isHi),
                            fontWeight = FontWeight.Bold,
                            color = BrandOrange,
                            fontSize = 15.sp
                        )
                        Text(
                            text = L10n.customerLabel(order.customerName, isHi),
                            color = BrandDark,
                            fontSize = 14.sp
                        )
                        Text(
                            text = L10n.serviceSummary(locService, order.weightKg, isHi),
                            color = TextSecondary,
                            fontSize = 13.sp
                        )
                        Text(
                            text = L10n.totalAmountLabel(CurrencyFormatter.formatInr(order.totalAmount), order.paymentStatus, isHi),
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
                        Text(L10n.done(isHi))
                    }
                },
                dismissButton = {
                    if (order.customerPhone.isNotBlank()) {
                        OutlinedButton(
                            onClick = {
                                val msg = if (isHi) {
                                    "नमस्ते ${order.customerName} जी, आपका ऑर्डर #${order.orderNumber} ($locService, ${order.weightKg} $weightUnit) $restaurantName में दर्ज हो चुका है। कुल राशि: ₹${order.totalAmount} ($paymentNote)। धन्यवाद!"
                                } else {
                                    "Namaste ${order.customerName} ji, your order #${order.orderNumber} ($locService, ${order.weightKg} KG) has been placed at $restaurantName. Total: ₹${order.totalAmount} ($paymentNote). Thank you!"
                                }
                                WhatsAppHelper.openChat(context, order.customerPhone, msg)
                            }
                        ) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.Chat,
                                contentDescription = null,
                                tint = Color(0xFF25D366),
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(L10n.whatsApp(isHi), color = Color(0xFF25D366))
                        }
                    }
                }
            )
        }
    }
}
