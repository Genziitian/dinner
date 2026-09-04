package com.dinepos.app.presentation.profile

import android.app.DatePickerDialog
import android.content.Intent
import android.net.Uri
import android.widget.Toast
import com.dinepos.app.core.utils.ExportDownloadHelper
import com.dinepos.app.core.utils.ExportDownloadResult
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
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
import androidx.compose.material.icons.automirrored.filled.OpenInNew
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.AdminUserDto
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProfileScreen(
    onNavigateBack: (() -> Unit)? = null,
    onNavigateToReports: () -> Unit = {},
    onNavigateToOrders: () -> Unit = {},
    onNavigateToItems: () -> Unit = {},
    onNavigateToBilling: () -> Unit = {},
    onNavigateToSummary: () -> Unit = {},
    onNavigateToPrivacy: () -> Unit = {},
    onNavigateToTerms: () -> Unit = {},
    onNavigateToRates: () -> Unit = {},
    onLogout: () -> Unit = {}
) {
    val context = LocalContext.current
    val sessionManager = DinePosApp.instance.sessionManager
    val managerRepository = DinePosApp.instance.managerRepository
    val scope = rememberCoroutineScope()

    val username = remember { sessionManager.getUsername().ifBlank { "User" } }
    val role = remember { sessionManager.getUserRole().lowercase() }
    val userId = remember { sessionManager.getUserId() }
    val restaurantName = remember { sessionManager.getRestaurantName() }
    val restaurantAddress = remember { sessionManager.getRestaurantAddress() }
    val restaurantPhone = remember { sessionManager.getRestaurantPhone() }
    val isMill = remember { sessionManager.getShopType().equals("mill", ignoreCase = true) }
    val shopLabel = if (isMill) "MILL" else "Restaurant"
    var currentBaseUrl by remember { mutableStateOf(sessionManager.getBaseUrl()) }

    var showLogoutDialog by remember { mutableStateOf(false) }
    var showStaffManagementDialog by remember { mutableStateOf(false) }
    var showExportDialog by remember { mutableStateOf(false) }
    val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            Column {
                TopAppBar(
                    title = {
                        Text(
                            text = if (isHi) "मेरी प्रोफ़ाइल" else "My Profile",
                            fontWeight = FontWeight.ExtraBold,
                            fontSize = 20.sp,
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
                        IconButton(onClick = { showLogoutDialog = true }) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.Logout,
                                contentDescription = "Logout",
                                tint = StatusError
                            )
                        }
                    },
                    colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White)
                )
                HorizontalDivider(color = BrandBorder.copy(alpha = 0.7f), thickness = 1.dp)
            }
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // 1. User Header & Real Username Card
            Card(
                shape = RoundedCornerShape(20.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(20.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    // Avatar Circle
                    Box(
                        modifier = Modifier
                            .size(76.dp)
                            .clip(CircleShape)
                            .background(BrandOrange.copy(alpha = 0.12f)),
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Outlined.Person,
                            contentDescription = "User Avatar",
                            tint = BrandOrange,
                            modifier = Modifier.size(38.dp)
                        )
                    }

                    Spacer(modifier = Modifier.height(14.dp))

                    Text(
                        text = username,
                        style = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    Spacer(modifier = Modifier.height(14.dp))
                    HorizontalDivider(color = BrandBorder.copy(alpha = 0.6f))
                    Spacer(modifier = Modifier.height(14.dp))

                    // Real Username row in place of User ID / Account Status / Security
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(BrandBackground, shape = RoundedCornerShape(10.dp))
                            .border(BorderStroke(1.dp, BrandBorder), shape = RoundedCornerShape(10.dp))
                            .padding(horizontal = 16.dp, vertical = 12.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = if (isHi) "यूज़रनेम" else "Username",
                            fontSize = 13.sp,
                            fontWeight = FontWeight.Medium,
                            color = TextSecondary
                        )
                        Text(
                            text = username,
                            fontSize = 15.sp,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    }
                }
            }

            // 2. Organization / Shop Outlet Details Card (Strictly Read-Only for Manager & Cashier)
            Card(
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = if (isHi) "मिल की जानकारी" else "${shopLabel} Outlet Details",
                            style = MaterialTheme.typography.titleSmall,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        if (role != "superadmin") {
                            Surface(
                                color = Color(0xFFF1F5F9),
                                shape = RoundedCornerShape(50)
                            ) {
                                Text(
                                    text = if (isHi) "केवल पढ़ने योग्य" else "Read-Only",
                                    fontSize = 10.5.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = TextMuted,
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                                )
                            }
                        }
                    }

                    if (role == "superadmin") {
                        InfoRow(
                            icon = Icons.Outlined.Business,
                            label = "System Scope",
                            value = "Master Multi-Tenant Console (Global Access)"
                        )
                        InfoRow(
                            icon = Icons.Outlined.AdminPanelSettings,
                            label = "Privileges",
                            value = "Full Platform Administration & Restaurant Provisioning"
                        )
                    } else {
                        InfoRow(
                            icon = Icons.Outlined.Storefront,
                            label = if (isHi) "मिल का नाम" else "${shopLabel} Name",
                            value = restaurantName.ifBlank { "Main ${shopLabel} Branch" }
                        )
                        if (restaurantAddress.isNotBlank()) {
                            InfoRow(
                                icon = Icons.Outlined.LocationOn,
                                label = if (isHi) "पता" else "Outlet Address",
                                value = restaurantAddress
                            )
                        }
                        if (restaurantPhone.isNotBlank()) {
                            InfoRow(
                                icon = Icons.Outlined.Phone,
                                label = if (isHi) "संपर्क नंबर" else "Contact Phone",
                                value = restaurantPhone
                            )
                        }
                        Text(
                            text = if (isHi) "मिल का विवरण केवल मुख्य एडमिनिस्ट्रेटर द्वारा बदला जा सकता है।" else "Branch details and locations can only be modified by Super Administrator.",
                            fontSize = 11.sp,
                            color = TextMuted,
                            modifier = Modifier.padding(top = 2.dp)
                        )
                    }
                }
            }

            // 3. Manager Exclusive Tools: Staff Management & CSV Exports
            if (role == "manager") {
                Card(
                    shape = RoundedCornerShape(18.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 1.5.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(16.dp),
                        verticalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        Text(
                            text = if (isHi) "मिल प्रबंधक टूल्स" else "${shopLabel} Manager Tools",
                            style = MaterialTheme.typography.titleSmall,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )

                        // 1. Staff Management (Only for Restaurant, HIDDEN FOR MILL)
                        if (!isMill) {
                            ProfileNavOption(
                                icon = Icons.Outlined.People,
                                title = "Staff Management (Cashiers)",
                                subtitle = "Create & reset passwords for cashier staff (double confirmation)",
                                onClick = { showStaffManagementDialog = true }
                            )
                        } else {
                            // Grinding Rates (Moved to Profile for Mill)
                            ProfileNavOption(
                                icon = Icons.Outlined.Storefront,
                                title = if (isHi) "पिसाई दरें" else "Grinding Rates",
                                subtitle = if (isHi) "अनाज पिसाई दरें प्रबंधित करें" else "Manage grain services and price per KG",
                                onClick = onNavigateToRates
                            )
                        }

                        // 2. CSV Exports (Sales data downloads)
                        ProfileNavOption(
                            icon = Icons.Outlined.FileDownload,
                            title = if (isHi) "बिक्री रिपोर्ट डाउनलोड करें (CSV)" else "Export Sales Reports (CSV)",
                            subtitle = if (isHi) "आज, मासिक या चुनी गई तारीख की एक्सेल/CSV रिपोर्ट" else "Download Excel/CSV reports for today, monthly or custom range",
                            onClick = { showExportDialog = true }
                        )
                    }
                }
            }

            // 5. System Shortcuts (For Superadmin only)
            if (role == "superadmin") {
                Card(
                    shape = RoundedCornerShape(18.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(16.dp),
                        verticalArrangement = Arrangement.spacedBy(10.dp)
                    ) {
                        Text(
                            text = "System Shortcuts",
                            style = MaterialTheme.typography.titleSmall,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )

                        ProfileNavOption(
                            icon = Icons.Outlined.Language,
                            title = "Open Web Super Admin Portal",
                            subtitle = "Manage restaurants, multi-tenancy & database",
                            onClick = {
                                val intent = Intent(Intent.ACTION_VIEW, Uri.parse("${currentBaseUrl}admin/dashboard"))
                                context.startActivity(intent)
                            }
                        )
                    }
                }
            }

            // 6. Language Preference (Hindi or English)
            var showLanguageDialog by remember { mutableStateOf(false) }
            var currentLanguage by remember { mutableStateOf(sessionManager.getLanguage()) }

            Card(
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    Text(
                        text = if (isHi) "भाषा चयन" else "Language / भाषा",
                        style = MaterialTheme.typography.titleSmall,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    ProfileNavOption(
                        icon = Icons.Outlined.Language,
                        title = if (isHi) "ऐप की भाषा" else "App Language",
                        subtitle = if (currentLanguage == "hi") "हिंदी (Hindi)" else "English",
                        onClick = { showLanguageDialog = true }
                    )
                }
            }

            if (showLanguageDialog) {
                AlertDialog(
                    onDismissRequest = { showLanguageDialog = false },
                    title = {
                        Text(
                            text = "Select Language / भाषा चुनें",
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    },
                    text = {
                        Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            LanguageOptionItem(
                                title = "English",
                                subtitle = "Default application language",
                                isSelected = currentLanguage == "en",
                                onClick = {
                                    sessionManager.setLanguage("en")
                                    currentLanguage = "en"
                                    Toast.makeText(context, "Language set to English", Toast.LENGTH_SHORT).show()
                                    showLanguageDialog = false
                                }
                            )
                            LanguageOptionItem(
                                title = "हिंदी (Hindi)",
                                subtitle = "हिंदी भाषा में उपयोग करें",
                                isSelected = currentLanguage == "hi",
                                onClick = {
                                    sessionManager.setLanguage("hi")
                                    currentLanguage = "hi"
                                    Toast.makeText(context, "भाषा हिंदी पर सेट की गई", Toast.LENGTH_SHORT).show()
                                    showLanguageDialog = false
                                }
                            )
                        }
                    },
                    confirmButton = {
                        TextButton(onClick = { showLanguageDialog = false }) {
                            Text("Done", color = BrandOrange, fontWeight = FontWeight.Bold)
                        }
                    }
                )
            }

            // 7. Legal & App Information
            Card(
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "Legal & Application",
                        style = MaterialTheme.typography.titleSmall,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    ProfileNavOption(
                        icon = Icons.Outlined.PrivacyTip,
                        title = "Privacy Policy",
                        subtitle = "Read data handling & security standards",
                        onClick = onNavigateToPrivacy
                    )
                    ProfileNavOption(
                        icon = Icons.Outlined.Description,
                        title = "Terms and Conditions",
                        subtitle = "Software usage agreement & terms",
                        onClick = onNavigateToTerms
                    )

                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = "DinePOS Native Android Client • v1.0.0 Production",
                        fontSize = 11.sp,
                        color = TextMuted,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            }

            // 7. Sign Out Button
            Button(
                onClick = { showLogoutDialog = true },
                colors = ButtonDefaults.buttonColors(containerColor = StatusError.copy(alpha = 0.1f)),
                border = BorderStroke(1.dp, StatusError.copy(alpha = 0.4f)),
                shape = RoundedCornerShape(14.dp),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
            ) {
                Icon(
                    imageVector = Icons.AutoMirrored.Filled.Logout,
                    contentDescription = "Sign Out",
                    tint = StatusError,
                    modifier = Modifier.size(20.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                Text(
                    text = if (isHi) "अकाउंट से लॉगआउट करें" else "Sign Out Account",
                    color = StatusError,
                    fontWeight = FontWeight.Bold,
                    fontSize = 15.sp
                )
            }

            Spacer(modifier = Modifier.height(24.dp))
        }
    }

    // Manager Staff Management Dialog
    if (showStaffManagementDialog) {
        ManagerStaffDialog(
            onDismiss = { showStaffManagementDialog = false }
        )
    }

    // Manager CSV Export Dialog
    if (showExportDialog) {
        ManagerExportDialog(
            baseUrl = currentBaseUrl,
            isMill = isMill,
            isHi = isHi,
            onDismiss = { showExportDialog = false }
        )
    }

    // Logout Confirmation Dialog
    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            containerColor = Color.White,
            titleContentColor = BrandDark,
            textContentColor = TextPrimary,
            shape = RoundedCornerShape(20.dp),
            title = { Text(text = "Sign Out", fontWeight = FontWeight.ExtraBold, color = BrandDark) },
            text = { Text("Are you sure you want to sign out of your account on this device?", color = TextSecondary, fontSize = 14.sp) },
            confirmButton = {
                Button(
                    onClick = {
                        showLogoutDialog = false
                        sessionManager.clearSession()
                        onLogout()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = StatusError),
                    shape = RoundedCornerShape(12.dp)
                ) {
                    Text("Yes, Sign Out", fontWeight = FontWeight.Bold, color = Color.White)
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutDialog = false }) {
                    Text("Cancel", fontWeight = FontWeight.SemiBold, color = TextSecondary)
                }
            }
        )
    }

}

/**
 * Manager Staff Dialog (View cashiers, Add cashier & Edit/Reset Cashier with double password confirmation)
 */
@Composable
private fun ManagerStaffDialog(
    onDismiss: () -> Unit
) {
    val context = LocalContext.current
    val managerRepository = DinePosApp.instance.managerRepository
    val scope = rememberCoroutineScope()

    var staffList by remember { mutableStateOf<List<AdminUserDto>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var showAddCashierDialog by remember { mutableStateOf(false) }
    var editingCashier by remember { mutableStateOf<AdminUserDto?>(null) }

    fun loadStaff() {
        scope.launch {
            isLoading = true
            when (val res = managerRepository.getManagerStaff()) {
                is Resource.Success -> {
                    staffList = res.data
                    isLoading = false
                }
                is Resource.Error -> {
                    isLoading = false
                    Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
                }
                else -> {
                    isLoading = false
                }
            }
        }
    }

    LaunchedEffect(Unit) {
        loadStaff()
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text("Staff Management", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    Text("Cashier accounts for this outlet", fontSize = 11.sp, color = TextSecondary)
                }
                IconButton(onClick = { showAddCashierDialog = true }) {
                    Icon(Icons.Default.PersonAdd, contentDescription = "Add Cashier", tint = BrandOrange)
                }
            }
        },
        text = {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .heightIn(max = 400.dp)
            ) {
                Button(
                    onClick = { showAddCashierDialog = true },
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Icon(Icons.Default.Add, contentDescription = null, modifier = Modifier.size(16.dp))
                    Spacer(modifier = Modifier.width(6.dp))
                    Text("+ Add Cashier Staff", fontWeight = FontWeight.Bold, fontSize = 13.sp)
                }

                Spacer(modifier = Modifier.height(12.dp))

                if (isLoading) {
                    Box(modifier = Modifier.fillMaxWidth().padding(24.dp), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = BrandOrange)
                    }
                } else if (staffList.isEmpty()) {
                    Box(modifier = Modifier.fillMaxWidth().padding(24.dp), contentAlignment = Alignment.Center) {
                        Text(text = "No Cashier Staff registered yet.\nTap '+ Add Cashier Staff' above.", fontSize = 12.sp, color = TextSecondary, textAlign = TextAlign.Center)
                    }
                } else {
                    LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        items(staffList, key = { it.id }) { staff ->
                            val isCashier = staff.role == "cashier"
                            val isActive = staff.status == "active"

                            Surface(
                                shape = RoundedCornerShape(12.dp),
                                border = BorderStroke(1.dp, BrandBorder),
                                color = BrandBackground,
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                Row(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .padding(10.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Box(
                                        modifier = Modifier
                                            .size(34.dp)
                                            .clip(CircleShape)
                                            .background(if (isCashier) Color(0xFF10B981).copy(alpha = 0.15f) else BrandOrange.copy(alpha = 0.15f)),
                                        contentAlignment = Alignment.Center
                                    ) {
                                        Icon(
                                            imageVector = Icons.Outlined.Person,
                                            contentDescription = null,
                                            tint = if (isCashier) Color(0xFF10B981) else BrandOrange,
                                            modifier = Modifier.size(18.dp)
                                        )
                                    }

                                    Spacer(modifier = Modifier.width(10.dp))

                                    Column(modifier = Modifier.weight(1f)) {
                                        Text(
                                            text = staff.username,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 13.sp,
                                            color = BrandDark
                                        )
                                        Text(
                                            text = if (isCashier) "Cashier Operator" else "Manager (You)",
                                            fontSize = 11.sp,
                                            color = if (isCashier) Color(0xFF10B981) else BrandDark
                                        )
                                    }

                                    if (isCashier) {
                                        IconButton(
                                            onClick = { editingCashier = staff },
                                            modifier = Modifier.size(32.dp)
                                        ) {
                                            Icon(Icons.Default.Edit, contentDescription = "Edit / Reset Password", tint = BrandDark, modifier = Modifier.size(16.dp))
                                        }

                                        TextButton(
                                            onClick = {
                                                scope.launch {
                                                    when (val toggleRes = managerRepository.toggleManagerStaff(staff.id)) {
                                                        is Resource.Success -> {
                                                            Toast.makeText(context, "Status updated!", Toast.LENGTH_SHORT).show()
                                                            loadStaff()
                                                        }
                                                        is Resource.Error -> {
                                                            Toast.makeText(context, toggleRes.message, Toast.LENGTH_SHORT).show()
                                                        }
                                                        else -> {}
                                                    }
                                                }
                                            },
                                            contentPadding = PaddingValues(horizontal = 4.dp)
                                        ) {
                                            Text(
                                                text = if (isActive) "Active" else "Inactive",
                                                fontSize = 11.sp,
                                                fontWeight = FontWeight.Bold,
                                                color = if (isActive) StatusSuccess else StatusError
                                            )
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        },
        confirmButton = {
            TextButton(onClick = onDismiss) {
                Text("Close")
            }
        }
    )

    // Add Cashier Dialog with DOUBLE PASSWORD CONFIRMATION
    if (showAddCashierDialog) {
        var cashierUsername by remember { mutableStateOf("") }
        var cashierPassword by remember { mutableStateOf("") }
        var confirmPassword by remember { mutableStateOf("") }
        var passwordVisible by remember { mutableStateOf(false) }
        var confirmPasswordVisible by remember { mutableStateOf(false) }
        var isSubmitting by remember { mutableStateOf(false) }

        val passwordsMatch = cashierPassword.isNotEmpty() && cashierPassword == confirmPassword
        val hasMinLength = cashierPassword.length >= 8

        AlertDialog(
            onDismissRequest = { showAddCashierDialog = false },
            title = {
                Column {
                    Text("Add Cashier Staff", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    Text("Managers can create Cashier accounts only", fontSize = 11.sp, color = TextSecondary)
                }
            },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    // Double confirmation alert notice
                    Surface(
                        color = Color(0xFFEFF6FF),
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, Color(0xFFBFDBFE))
                    ) {
                        Row(modifier = Modifier.padding(8.dp), verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                imageVector = Icons.Outlined.Info,
                                contentDescription = null,
                                tint = Color(0xFF1E40AF),
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "Double confirmation required: verify password before saving.",
                                fontSize = 11.sp,
                                color = Color(0xFF1E40AF),
                                lineHeight = 14.sp
                            )
                        }
                    }

                    OutlinedTextField(
                        value = cashierUsername,
                        onValueChange = { cashierUsername = it.lowercase().trim() },
                        label = { Text("Cashier Username (3-10 chars) *") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = cashierPassword,
                        onValueChange = { cashierPassword = it },
                        label = { Text("Password (min 8 chars) *") },
                        singleLine = true,
                        visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                        trailingIcon = {
                            IconButton(onClick = { passwordVisible = !passwordVisible }) {
                                Icon(
                                    imageVector = if (passwordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                    contentDescription = "Toggle password visibility",
                                    tint = TextSecondary
                                )
                            }
                        },
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it },
                        label = { Text("Confirm Password *") },
                        singleLine = true,
                        visualTransformation = if (confirmPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                        trailingIcon = {
                            IconButton(onClick = { confirmPasswordVisible = !confirmPasswordVisible }) {
                                Icon(
                                    imageVector = if (confirmPasswordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                    contentDescription = "Toggle confirm password visibility",
                                    tint = TextSecondary
                                )
                            }
                        },
                        isError = confirmPassword.isNotEmpty() && !passwordsMatch,
                        supportingText = {
                            if (confirmPassword.isNotEmpty()) {
                                if (passwordsMatch) {
                                    Text("Passwords match", color = StatusSuccess, fontSize = 11.sp)
                                } else {
                                    Text("Passwords do not match", color = StatusError, fontSize = 11.sp)
                                }
                            }
                        },
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        if (cashierUsername.isNotBlank() && passwordsMatch && hasMinLength) {
                            scope.launch {
                                isSubmitting = true
                                when (val res = managerRepository.createCashierStaff(cashierUsername, cashierPassword, confirmPassword)) {
                                    is Resource.Success -> {
                                        isSubmitting = false
                                        showAddCashierDialog = false
                                        Toast.makeText(context, "Cashier '${res.data.username}' created successfully!", Toast.LENGTH_SHORT).show()
                                        loadStaff()
                                    }
                                    is Resource.Error -> {
                                        isSubmitting = false
                                        Toast.makeText(context, res.message, Toast.LENGTH_LONG).show()
                                    }
                                    else -> {
                                        isSubmitting = false
                                    }
                                }
                            }
                        }
                    },
                    enabled = cashierUsername.isNotBlank() && passwordsMatch && hasMinLength && !isSubmitting,
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                ) {
                    if (isSubmitting) {
                        CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                    } else {
                        Text("Create Cashier")
                    }
                }
            },
            dismissButton = {
                TextButton(onClick = { showAddCashierDialog = false }) {
                    Text("Cancel")
                }
            }
        )
    }

    // Edit Cashier & Reset Password Dialog (Manager only)
    if (editingCashier != null) {
        val cashier = editingCashier!!
        var usernameVal by remember { mutableStateOf(cashier.username) }
        var newPass by remember { mutableStateOf("") }
        var confirmNewPass by remember { mutableStateOf("") }
        var passwordVisible by remember { mutableStateOf(false) }
        var confirmPasswordVisible by remember { mutableStateOf(false) }
        var isSubmitting by remember { mutableStateOf(false) }

        val passwordsMatch = newPass.isEmpty() || (newPass == confirmNewPass && newPass.length >= 8)

        AlertDialog(
            onDismissRequest = { editingCashier = null },
            title = {
                Column {
                    Text("Edit Cashier / Reset Password", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    Text("Cashier: ${cashier.username}", fontSize = 11.sp, color = TextSecondary)
                }
            },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    Surface(
                        color = Color(0xFFEFF6FF),
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, Color(0xFFBFDBFE))
                    ) {
                        Row(modifier = Modifier.padding(8.dp), verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                imageVector = Icons.Outlined.Info,
                                contentDescription = null,
                                tint = Color(0xFF1E40AF),
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "Only Manager can modify cashier credentials.",
                                fontSize = 11.sp,
                                color = Color(0xFF1E40AF),
                                lineHeight = 14.sp
                            )
                        }
                    }

                    OutlinedTextField(
                        value = usernameVal,
                        onValueChange = { usernameVal = it.lowercase().trim() },
                        label = { Text("Username *") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Text(text = "Reset Password (leave blank to keep existing):", fontSize = 11.5.sp, color = TextMuted)

                    OutlinedTextField(
                        value = newPass,
                        onValueChange = { newPass = it },
                        label = { Text("New Password (min 8 chars)") },
                        singleLine = true,
                        visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                        trailingIcon = {
                            IconButton(onClick = { passwordVisible = !passwordVisible }) {
                                Icon(
                                    imageVector = if (passwordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                    contentDescription = "Toggle password visibility",
                                    tint = TextSecondary
                                )
                            }
                        },
                        modifier = Modifier.fillMaxWidth()
                    )

                    if (newPass.isNotEmpty()) {
                        OutlinedTextField(
                            value = confirmNewPass,
                            onValueChange = { confirmNewPass = it },
                            label = { Text("Confirm New Password *") },
                            singleLine = true,
                            visualTransformation = if (confirmPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                            trailingIcon = {
                                IconButton(onClick = { confirmPasswordVisible = !confirmPasswordVisible }) {
                                    Icon(
                                        imageVector = if (confirmPasswordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                                        contentDescription = "Toggle confirm password visibility",
                                        tint = TextSecondary
                                    )
                                }
                            },
                            isError = newPass != confirmNewPass,
                            supportingText = {
                                if (confirmNewPass.isNotEmpty()) {
                                    if (newPass == confirmNewPass) Text("Passwords match", color = StatusSuccess, fontSize = 11.sp)
                                    else Text("Passwords do not match", color = StatusError, fontSize = 11.sp)
                                }
                            },
                            modifier = Modifier.fillMaxWidth()
                        )
                    }
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        if (usernameVal.isNotBlank() && passwordsMatch) {
                            scope.launch {
                                isSubmitting = true
                                when (val res = managerRepository.updateManagerStaff(cashier.id, usernameVal, newPass, confirmNewPass)) {
                                    is Resource.Success -> {
                                        isSubmitting = false
                                        editingCashier = null
                                        Toast.makeText(context, "Cashier staff updated successfully!", Toast.LENGTH_SHORT).show()
                                        loadStaff()
                                    }
                                    is Resource.Error -> {
                                        isSubmitting = false
                                        Toast.makeText(context, res.message, Toast.LENGTH_LONG).show()
                                    }
                                    else -> {
                                        isSubmitting = false
                                    }
                                }
                            }
                        }
                    },
                    enabled = usernameVal.isNotBlank() && passwordsMatch && !isSubmitting,
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                ) {
                    if (isSubmitting) {
                        CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                    } else {
                        Text("Save Cashier")
                    }
                }
            },
            dismissButton = {
                TextButton(onClick = { editingCashier = null }) {
                    Text("Cancel")
                }
            }
        )
    }
}

/**
 * Manager CSV Export Dialog (Exports daily, monthly, and custom range sales to CSV, or mill JSON backup)
 */
@Composable
private fun ManagerExportDialog(
    baseUrl: String,
    isMill: Boolean = false,
    isHi: Boolean = false,
    onDismiss: () -> Unit
) {
    val context = LocalContext.current
    val managerRepository = DinePosApp.instance.managerRepository
    val millRepository = DinePosApp.instance.millRepository
    val scope = rememberCoroutineScope()

    val isoDateFormat = remember { SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()) }
    val currentMonthFormat = remember { SimpleDateFormat("yyyy-MM", Locale.getDefault()) }

    val todayDate = remember { isoDateFormat.format(Date()) }
    val currentMonth = remember { currentMonthFormat.format(Date()) }

    var exportType by remember { mutableStateOf("daily") } // "daily", "monthly", "custom", "mill_backup"
    var selectedDate by remember { mutableStateOf(todayDate) }
    var selectedMonth by remember { mutableStateOf(currentMonth) }
    var startDate by remember { mutableStateOf(todayDate) }
    var endDate by remember { mutableStateOf(todayDate) }

    var isExporting by remember { mutableStateOf(false) }
    var downloadedResult by remember { mutableStateOf<ExportDownloadResult?>(null) }

    fun showDatePicker(initialDate: String, onDateSelected: (String) -> Unit) {
        val cal = Calendar.getInstance()
        try {
            isoDateFormat.parse(initialDate)?.let { cal.time = it }
        } catch (_: Exception) {}

        DatePickerDialog(
            context,
            { _, year, month, dayOfMonth ->
                val chosen = Calendar.getInstance().apply {
                    set(year, month, dayOfMonth)
                }
                onDateSelected(isoDateFormat.format(chosen.time))
            },
            cal.get(Calendar.YEAR),
            cal.get(Calendar.MONTH),
            cal.get(Calendar.DAY_OF_MONTH)
        ).show()
    }

    fun triggerExport(shareAfterDownload: Boolean = false) {
        scope.launch {
            isExporting = true
            if (exportType == "mill_backup") {
                when (val res = millRepository.getBackup()) {
                    is Resource.Success -> {
                        isExporting = false
                        val filename = "Mill_Data_Backup_${SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(Date())}.json"
                        val result = ExportDownloadHelper.saveToDownloads(
                            context = context,
                            filename = filename,
                            content = res.data,
                            mimeType = "application/json"
                        )
                        if (result.success) {
                            Toast.makeText(
                                context,
                                if (isHi) "बैकअप डाउनलोड फोल्डर में सुरक्षित हो गया" else "Backup saved to Downloads/DinePOS",
                                Toast.LENGTH_SHORT
                            ).show()
                            if (shareAfterDownload && result.contentUri != null) {
                                ExportDownloadHelper.shareFile(
                                    context = context,
                                    contentUri = result.contentUri,
                                    subject = "Mill Backup Data",
                                    mimeType = "application/json"
                                )
                            } else {
                                downloadedResult = result
                            }
                        } else {
                            Toast.makeText(context, result.message, Toast.LENGTH_LONG).show()
                        }
                    }
                    is Resource.Error -> {
                        isExporting = false
                        Toast.makeText(context, res.message ?: "Failed to export backup", Toast.LENGTH_LONG).show()
                    }
                    else -> {
                        isExporting = false
                    }
                }
                return@launch
            }

            val rangeLabel = when (exportType) {
                "daily" -> selectedDate
                "monthly" -> selectedMonth
                else -> "${startDate}_to_${endDate}"
            }

            when (val res = managerRepository.getExportData(
                type = exportType,
                date = if (exportType == "daily") selectedDate else null,
                month = if (exportType == "monthly") selectedMonth else null,
                startDate = if (exportType == "custom") startDate else null,
                endDate = if (exportType == "custom") endDate else null
            )) {
                is Resource.Success -> {
                    isExporting = false
                    val data = res.data
                    val csvText = ExportDownloadHelper.buildSalesCsv(data, isMill = isMill)
                    val filename = ExportDownloadHelper.generateFilename(
                        businessName = data.restaurantName,
                        type = if (isMill) "MillSales" else "SalesReport",
                        rangeLabel = rangeLabel,
                        extension = "csv"
                    )

                    val result = ExportDownloadHelper.saveToDownloads(
                        context = context,
                        filename = filename,
                        content = csvText,
                        mimeType = "text/csv"
                    )

                    if (result.success) {
                        Toast.makeText(
                            context,
                            if (isHi) "रिपोर्ट डाउनलोड हो गई: $filename" else "Report saved to Downloads/DinePOS: $filename",
                            Toast.LENGTH_SHORT
                        ).show()

                        if (shareAfterDownload && result.contentUri != null) {
                            ExportDownloadHelper.shareFile(
                                context = context,
                                contentUri = result.contentUri,
                                subject = "${data.restaurantName} Sales Export ($rangeLabel)",
                                mimeType = "text/csv"
                            )
                        } else {
                            downloadedResult = result
                        }
                    } else {
                        Toast.makeText(context, result.message, Toast.LENGTH_LONG).show()
                    }
                }
                is Resource.Error -> {
                    isExporting = false
                    Toast.makeText(context, res.message ?: "Failed to generate export", Toast.LENGTH_LONG).show()
                }
                else -> {
                    isExporting = false
                }
            }
        }
    }

    if (downloadedResult != null) {
        val res = downloadedResult!!
        AlertDialog(
            onDismissRequest = {
                downloadedResult = null
                onDismiss()
            },
            icon = {
                Icon(
                    imageVector = Icons.Outlined.CheckCircle,
                    contentDescription = null,
                    tint = BrandEmerald,
                    modifier = Modifier.size(36.dp)
                )
            },
            title = {
                Text(
                    text = if (isHi) "फ़ाइल डाउनलोड हो गई!" else "Download Complete!",
                    fontWeight = FontWeight.Bold,
                    fontSize = 17.sp
                )
            },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(
                        text = if (isHi) "रिपोर्ट आपके डिवाइस के Downloads फ़ोल्डर में सुरक्षित हो गई है:" else "Report spreadsheet saved directly to your device:",
                        fontSize = 13.sp,
                        color = TextSecondary
                    )
                    Surface(
                        color = BrandSurface,
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(10.dp)) {
                            Text(
                                text = res.filename,
                                fontWeight = FontWeight.Bold,
                                fontSize = 12.sp,
                                color = BrandDark
                            )
                            Spacer(modifier = Modifier.height(2.dp))
                            Text(
                                text = "Path: ${res.relativePath}",
                                fontSize = 11.sp,
                                color = TextMuted
                            )
                        }
                    }
                }
            },
            confirmButton = {
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    if (res.contentUri != null) {
                        OutlinedButton(
                            onClick = {
                                ExportDownloadHelper.shareFile(
                                    context = context,
                                    contentUri = res.contentUri,
                                    subject = res.filename,
                                    mimeType = if (res.filename.endsWith(".json")) "application/json" else "text/csv"
                                )
                            },
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Icon(Icons.Outlined.Share, contentDescription = null, modifier = Modifier.size(16.dp))
                            Spacer(modifier = Modifier.width(4.dp))
                            Text(if (isHi) "शेयर करें" else "Share")
                        }

                        Button(
                            onClick = {
                                ExportDownloadHelper.openFile(
                                    context = context,
                                    contentUri = res.contentUri,
                                    mimeType = if (res.filename.endsWith(".json")) "application/json" else "text/csv"
                                )
                            },
                            colors = ButtonDefaults.buttonColors(containerColor = BrandEmerald),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Icon(Icons.AutoMirrored.Filled.OpenInNew, contentDescription = null, modifier = Modifier.size(16.dp))
                            Spacer(modifier = Modifier.width(4.dp))
                            Text(if (isHi) "खोलें (Excel)" else "Open File")
                        }
                    } else {
                        Button(
                            onClick = {
                                downloadedResult = null
                                onDismiss()
                            },
                            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                        ) {
                            Text(if (isHi) "ठीक है" else "Done")
                        }
                    }
                }
            },
            dismissButton = {
                TextButton(onClick = {
                    downloadedResult = null
                    onDismiss()
                }) {
                    Text(if (isHi) "बंद करें" else "Close")
                }
            }
        )
        return
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = {
            Column {
                Text(
                    text = if (isHi) "बिक्री रिपोर्ट डाउनलोड करें (CSV)" else "Export Sales Data (CSV)",
                    fontWeight = FontWeight.Bold,
                    fontSize = 16.sp
                )
                Text(
                    text = if (isHi) "एक्सेल के लिए स्प्रेडशीट सीधे डिवाइस में डाउनलोड करें" else "Download Excel-ready spreadsheets directly to device",
                    fontSize = 11.sp,
                    color = TextSecondary
                )
            }
        },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                // Info description banner
                Surface(
                    color = BrandOrange.copy(alpha = 0.08f),
                    shape = RoundedCornerShape(10.dp),
                    border = BorderStroke(1.dp, BrandOrange.copy(alpha = 0.25f))
                ) {
                    Column(modifier = Modifier.padding(10.dp)) {
                        Text(
                            text = if (isHi) "इन-ऐप डाउनलोड सुविधा" else "Direct In-App Download",
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold,
                            color = BrandOrange
                        )
                        Spacer(modifier = Modifier.height(2.dp))
                        Text(
                            text = if (isHi)
                                "फ़ाइल सीधे आपके फोन के 'Downloads/DinePOS' फ़ोल्डर में सहेजी जाती है। इसे एक्सेल या गूगल शीट्स में खोला जा सकता है।"
                            else
                                "Exports full orders, revenue totals, items, and billing logs directly to Downloads/DinePOS folder for Excel or Google Sheets.",
                            fontSize = 11.sp,
                            color = TextSecondary,
                            lineHeight = 14.sp
                        )
                    }
                }

                // Range Selector Buttons
                Text(
                    text = if (isHi) "अवधि चुनें:" else "Choose Export Period:",
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    color = BrandDark
                )

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(6.dp)
                ) {
                    OutlinedButton(
                        onClick = { exportType = "daily" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (exportType == "daily") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (exportType == "daily") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f),
                        contentPadding = PaddingValues(horizontal = 4.dp, vertical = 6.dp)
                    ) {
                        Text(if (isHi) "आज" else "Today", fontSize = 11.5.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }

                    OutlinedButton(
                        onClick = { exportType = "monthly" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (exportType == "monthly") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (exportType == "monthly") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f),
                        contentPadding = PaddingValues(horizontal = 4.dp, vertical = 6.dp)
                    ) {
                        Text(if (isHi) "इस महीने" else "Month", fontSize = 11.5.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }

                    OutlinedButton(
                        onClick = { exportType = "custom" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (exportType == "custom") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (exportType == "custom") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f),
                        contentPadding = PaddingValues(horizontal = 4.dp, vertical = 6.dp)
                    ) {
                        Text(if (isHi) "कस्टम" else "Custom", fontSize = 11.5.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }

                    if (isMill) {
                        OutlinedButton(
                            onClick = { exportType = "mill_backup" },
                            colors = ButtonDefaults.outlinedButtonColors(
                                containerColor = if (exportType == "mill_backup") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                            ),
                            border = BorderStroke(1.dp, if (exportType == "mill_backup") BrandOrange else BrandBorder),
                            shape = RoundedCornerShape(10.dp),
                            modifier = Modifier.weight(1f),
                            contentPadding = PaddingValues(horizontal = 4.dp, vertical = 6.dp)
                        ) {
                            Text(if (isHi) "बैकअप" else "Backup", fontSize = 11.5.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                        }
                    }
                }

                if (exportType == "daily") {
                    OutlinedTextField(
                        value = selectedDate,
                        onValueChange = { selectedDate = it },
                        label = { Text(if (isHi) "तारीख (YYYY-MM-DD)" else "Date (YYYY-MM-DD)") },
                        trailingIcon = {
                            IconButton(onClick = { showDatePicker(selectedDate) { selectedDate = it } }) {
                                Icon(Icons.Outlined.CalendarToday, contentDescription = "Pick Date", tint = BrandOrange)
                            }
                        },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                } else if (exportType == "monthly") {
                    OutlinedTextField(
                        value = selectedMonth,
                        onValueChange = { selectedMonth = it },
                        label = { Text(if (isHi) "महीना (YYYY-MM)" else "Month (YYYY-MM)") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                } else if (exportType == "custom") {
                    Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        OutlinedTextField(
                            value = startDate,
                            onValueChange = { startDate = it },
                            label = { Text(if (isHi) "शुरुआती तारीख" else "Start Date (YYYY-MM-DD)") },
                            trailingIcon = {
                                IconButton(onClick = { showDatePicker(startDate) { startDate = it } }) {
                                    Icon(Icons.Outlined.CalendarToday, contentDescription = "Pick Date", tint = BrandOrange)
                                }
                            },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )

                        OutlinedTextField(
                            value = endDate,
                            onValueChange = { endDate = it },
                            label = { Text(if (isHi) "अंतिम तारीख" else "End Date (YYYY-MM-DD)") },
                            trailingIcon = {
                                IconButton(onClick = { showDatePicker(endDate) { endDate = it } }) {
                                    Icon(Icons.Outlined.CalendarToday, contentDescription = "Pick Date", tint = BrandOrange)
                                }
                            },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth()
                        )
                    }
                } else if (exportType == "mill_backup") {
                    Surface(
                        color = BrandSurface,
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(10.dp)) {
                            Text(
                                text = if (isHi) "पूरा मिल बैकअप (JSON)" else "Full Mill Backup (JSON)",
                                fontWeight = FontWeight.Bold,
                                fontSize = 12.sp,
                                color = BrandDark
                            )
                            Spacer(modifier = Modifier.height(2.dp))
                            Text(
                                text = if (isHi)
                                    "सभी ग्राहकों का विवरण, पिसाई सेवाएं और पूरे ऑर्डर का पूरा डेटा JSON फ़ाइल में सुरक्षित करें।"
                                else
                                    "Exports all customer records, grinding rates, and complete order history into a JSON backup file.",
                                fontSize = 11.sp,
                                color = TextSecondary
                            )
                        }
                    }
                }
            }
        },
        confirmButton = {
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedButton(
                    onClick = { triggerExport(shareAfterDownload = true) },
                    enabled = !isExporting,
                    shape = RoundedCornerShape(10.dp)
                ) {
                    Icon(Icons.Outlined.Share, contentDescription = null, modifier = Modifier.size(16.dp))
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(if (isHi) "शेयर" else "Share")
                }

                Button(
                    onClick = { triggerExport(shareAfterDownload = false) },
                    enabled = !isExporting,
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                    shape = RoundedCornerShape(10.dp)
                ) {
                    if (isExporting) {
                        CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                    } else {
                        Icon(Icons.Outlined.FileDownload, contentDescription = null, modifier = Modifier.size(16.dp))
                        Spacer(modifier = Modifier.width(4.dp))
                        Text(if (isHi) "डाउनलोड करें" else "Download")
                    }
                }
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text(if (isHi) "रद्द करें" else "Cancel")
            }
        }
    )
}

@Composable
private fun InfoRow(icon: ImageVector, label: String, value: String) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.Top
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = BrandOrange,
            modifier = Modifier.size(18.dp).padding(top = 2.dp)
        )
        Spacer(modifier = Modifier.width(10.dp))
        Column {
            Text(text = label, fontSize = 11.sp, color = TextMuted, fontWeight = FontWeight.Medium)
            Text(text = value, fontSize = 13.5.sp, fontWeight = FontWeight.SemiBold, color = BrandDark)
        }
    }
}

@Composable
private fun ProfileNavOption(
    icon: ImageVector,
    title: String,
    subtitle: String,
    onClick: () -> Unit
) {
    Surface(
        onClick = onClick,
        shape = RoundedCornerShape(12.dp),
        color = Color.Transparent,
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 8.dp, horizontal = 4.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(38.dp)
                    .clip(RoundedCornerShape(10.dp))
                    .background(BrandOrange.copy(alpha = 0.1f)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = icon,
                    contentDescription = null,
                    tint = BrandOrange,
                    modifier = Modifier.size(20.dp)
                )
            }
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(text = title, fontSize = 13.5.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                Text(text = subtitle, fontSize = 11.5.sp, color = TextSecondary)
            }
            Icon(
                imageVector = Icons.Default.ChevronRight,
                contentDescription = null,
                tint = TextMuted,
                modifier = Modifier.size(20.dp)
            )
        }
    }
}

@Composable
private fun LanguageOptionItem(
    title: String,
    subtitle: String,
    isSelected: Boolean,
    onClick: () -> Unit
) {
    Surface(
        onClick = onClick,
        shape = RoundedCornerShape(12.dp),
        color = if (isSelected) BrandOrange.copy(alpha = 0.08f) else Color.Transparent,
        border = BorderStroke(1.dp, if (isSelected) BrandOrange else BrandBorder.copy(alpha = 0.5f)),
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
                Text(
                    text = title,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp,
                    color = BrandDark
                )
                Text(
                    text = subtitle,
                    fontSize = 11.5.sp,
                    color = TextSecondary
                )
            }
            RadioButton(
                selected = isSelected,
                onClick = onClick,
                colors = RadioButtonDefaults.colors(selectedColor = BrandOrange)
            )
        }
    }
}

