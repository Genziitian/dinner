package com.dinepos.app.presentation.profile

import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
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
    var currentBaseUrl by remember { mutableStateOf(sessionManager.getBaseUrl()) }

    var showLogoutDialog by remember { mutableStateOf(false) }
    var showStaffManagementDialog by remember { mutableStateOf(false) }
    var showExportDialog by remember { mutableStateOf(false) }

    // Role specific badge and title
    val (roleTitle, roleBadgeColor, roleIcon) = when (role) {
        "superadmin" -> Triple("Master Super Administrator", Color(0xFFF59E0B), "👑")
        "manager" -> Triple("Restaurant General Manager", Color(0xFF3B82F6), "👔")
        "cashier" -> Triple("POS Billing Operator & Cashier", Color(0xFF10B981), "🛒")
        else -> Triple("Staff Member", TextSecondary, "👤")
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "My Profile",
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
                    IconButton(onClick = { showLogoutDialog = true }) {
                        Icon(
                            imageVector = Icons.AutoMirrored.Filled.Logout,
                            contentDescription = "Logout",
                            tint = StatusError
                        )
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
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // 1. User Header & Role Designation Card
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
                        Text(
                            text = roleIcon,
                            fontSize = 36.sp
                        )
                    }

                    Spacer(modifier = Modifier.height(12.dp))

                    Text(
                        text = username,
                        style = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    Spacer(modifier = Modifier.height(6.dp))

                    // Designation Badge
                    Surface(
                        color = roleBadgeColor.copy(alpha = 0.12f),
                        shape = RoundedCornerShape(50),
                        border = BorderStroke(1.dp, roleBadgeColor.copy(alpha = 0.3f))
                    ) {
                        Row(
                            modifier = Modifier.padding(horizontal = 14.dp, vertical = 6.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(text = roleIcon, fontSize = 13.sp)
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = roleTitle,
                                color = roleBadgeColor,
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold
                            )
                        }
                    }

                    Spacer(modifier = Modifier.height(14.dp))
                    HorizontalDivider(color = BrandBorder.copy(alpha = 0.6f))
                    Spacer(modifier = Modifier.height(12.dp))

                    // Account Stats Row
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceAround
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = "User ID", fontSize = 11.sp, color = TextMuted)
                            Text(
                                text = if (userId > 0) "#$userId" else "--",
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = "Account Status", fontSize = 11.sp, color = TextMuted)
                            Text(
                                text = "🟢 Active",
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = StatusSuccess
                            )
                        }
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = "Security", fontSize = 11.sp, color = TextMuted)
                            Text(
                                text = "🔒 Encrypted",
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                        }
                    }

                    // Security Authority Notice
                    if (role != "superadmin") {
                        Spacer(modifier = Modifier.height(12.dp))
                        Surface(
                            color = BrandBackground,
                            shape = RoundedCornerShape(8.dp),
                            border = BorderStroke(1.dp, BrandBorder)
                        ) {
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(8.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text(text = "🔒", fontSize = 12.sp)
                                Spacer(modifier = Modifier.width(6.dp))
                                Text(
                                    text = if (role == "manager")
                                        "Account & credentials managed by Super Administrator."
                                    else
                                        "Account & credentials managed by Restaurant Manager.",
                                    fontSize = 11.sp,
                                    color = TextSecondary,
                                    lineHeight = 14.sp
                                )
                            }
                        }
                    }
                }
            }

            // 2. Organization / Restaurant Outlet Details Card (Strictly Read-Only for Manager & Cashier)
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
                            text = "🏢 Restaurant Outlet Details",
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
                                    text = "🔒 Read-Only",
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
                            label = "Restaurant Name",
                            value = restaurantName.ifBlank { "Main Restaurant Branch" }
                        )
                        if (restaurantAddress.isNotBlank()) {
                            InfoRow(
                                icon = Icons.Outlined.LocationOn,
                                label = "Outlet Address",
                                value = restaurantAddress
                            )
                        }
                        if (restaurantPhone.isNotBlank()) {
                            InfoRow(
                                icon = Icons.Outlined.Phone,
                                label = "Contact Phone",
                                value = restaurantPhone
                            )
                        }
                        Text(
                            text = "💡 Branch details & locations can only be modified by Super Administrator.",
                            fontSize = 11.sp,
                            color = TextMuted,
                            modifier = Modifier.padding(top = 2.dp)
                        )
                    }
                }
            }

            // 3. Manager Exclusive Tools: Staff Management (Cashier creation & reset) & CSV Exports
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
                            text = "👔 Restaurant Manager Tools",
                            style = MaterialTheme.typography.titleSmall,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )

                        // 1. Staff Management (Cashier creation & password reset with double confirmation)
                        ProfileNavOption(
                            icon = Icons.Outlined.People,
                            title = "Staff Management (Cashiers)",
                            subtitle = "Create & reset passwords for cashier staff (double confirmation)",
                            onClick = { showStaffManagementDialog = true }
                        )

                        // 2. CSV Exports (Sales data downloads)
                        ProfileNavOption(
                            icon = Icons.Outlined.FileDownload,
                            title = "Export Sales Reports (CSV)",
                            subtitle = "Download Excel/CSV reports for today, monthly or custom range",
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
                            text = "⚡ System Shortcuts",
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

            // 6. Legal & App Information
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
                        text = "ℹ️ Legal & Application",
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
                    text = "Sign Out Account",
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
            onDismiss = { showExportDialog = false }
        )
    }

    // Logout Confirmation Dialog
    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            title = { Text(text = "Sign Out", fontWeight = FontWeight.Bold) },
            text = { Text("Are you sure you want to sign out of your account on this device?") },
            confirmButton = {
                Button(
                    onClick = {
                        showLogoutDialog = false
                        sessionManager.clearSession()
                        onLogout()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = StatusError)
                ) {
                    Text("Yes, Sign Out")
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutDialog = false }) {
                    Text("Cancel")
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
                                        Text(text = if (isCashier) "🛒" else "👔", fontSize = 16.sp)
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
                                                text = if (isActive) "🟢 Active" else "🔴 Inactive",
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
                            Text(text = "🛡️", fontSize = 14.sp)
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
                        visualTransformation = PasswordVisualTransformation(),
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it },
                        label = { Text("Confirm Password *") },
                        singleLine = true,
                        visualTransformation = PasswordVisualTransformation(),
                        isError = confirmPassword.isNotEmpty() && !passwordsMatch,
                        supportingText = {
                            if (confirmPassword.isNotEmpty()) {
                                if (passwordsMatch) {
                                    Text("🟢 Passwords match perfectly", color = StatusSuccess, fontSize = 11.sp)
                                } else {
                                    Text("❌ Passwords do not match", color = StatusError, fontSize = 11.sp)
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
                            Text(text = "🛡️", fontSize = 14.sp)
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "Only Restaurant Manager can modify cashier credentials.",
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
                        visualTransformation = PasswordVisualTransformation(),
                        modifier = Modifier.fillMaxWidth()
                    )

                    if (newPass.isNotEmpty()) {
                        OutlinedTextField(
                            value = confirmNewPass,
                            onValueChange = { confirmNewPass = it },
                            label = { Text("Confirm New Password *") },
                            singleLine = true,
                            visualTransformation = PasswordVisualTransformation(),
                            isError = newPass != confirmNewPass,
                            supportingText = {
                                if (confirmNewPass.isNotEmpty()) {
                                    if (newPass == confirmNewPass) Text("🟢 Passwords match", color = StatusSuccess, fontSize = 11.sp)
                                    else Text("❌ Passwords do not match", color = StatusError, fontSize = 11.sp)
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
 * Manager CSV Export Dialog (Exports daily, monthly, and custom range sales to CSV)
 */
@Composable
private fun ManagerExportDialog(
    baseUrl: String,
    onDismiss: () -> Unit
) {
    val context = LocalContext.current
    val managerRepository = DinePosApp.instance.managerRepository
    val scope = rememberCoroutineScope()

    val todayDate = remember { SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date()) }
    val currentMonth = remember { SimpleDateFormat("yyyy-MM", Locale.getDefault()).format(Date()) }

    var exportType by remember { mutableStateOf("daily") } // "daily", "monthly", "custom"
    var selectedDate by remember { mutableStateOf(todayDate) }
    var selectedMonth by remember { mutableStateOf(currentMonth) }
    var isExporting by remember { mutableStateOf(false) }

    fun triggerExport(shareDirectly: Boolean = true) {
        scope.launch {
            isExporting = true
            when (val res = managerRepository.getExportData(
                type = exportType,
                date = if (exportType == "daily") selectedDate else null,
                month = if (exportType == "monthly") selectedMonth else null
            )) {
                is Resource.Success -> {
                    isExporting = false
                    val data = res.data

                    if (shareDirectly && data.orders.isNotEmpty()) {
                        // Generate CSV Content string
                        val csvBuilder = StringBuilder()
                        csvBuilder.append("RESTAURANT SALES EXPORT REPORT\n")
                        csvBuilder.append("Restaurant Name,${data.restaurantName}\n")
                        csvBuilder.append("Export Range,${data.startDate} to ${data.endDate}\n")
                        csvBuilder.append("Total Orders,${data.stats?.totalOrders ?: data.orders.size}\n")
                        csvBuilder.append("Total Revenue (INR),${data.stats?.totalSales ?: 0.0}\n")
                        csvBuilder.append("Cash Sales (INR),${data.stats?.cashSales ?: 0.0}\n")
                        csvBuilder.append("Online Sales (INR),${data.stats?.onlineSales ?: 0.0}\n\n")

                        csvBuilder.append("Order #,Date,Time,Customer Name,Phone,Subtotal,Total,Payment Method,Status,Billed By\n")
                        for (order in data.orders) {
                            csvBuilder.append("${order.orderNumber},${order.orderDate},${order.orderTime},${order.customerName ?: "Walk-in"},${order.customerPhone ?: ""},${order.subtotal},${order.total},${order.paymentMethod},${order.status},${order.createdByUsername}\n")
                        }

                        val sendIntent = Intent().apply {
                            action = Intent.ACTION_SEND
                            putExtra(Intent.EXTRA_TEXT, csvBuilder.toString())
                            putExtra(Intent.EXTRA_SUBJECT, "${data.restaurantName} Sales Export (${data.startDate})")
                            type = "text/plain"
                        }
                        val shareIntent = Intent.createChooser(sendIntent, "Export Sales Report")
                        context.startActivity(shareIntent)
                    } else {
                        // Open direct web download link in browser
                        val downloadFullUrl = if (data.downloadUrl.startsWith("http")) data.downloadUrl else "${baseUrl}${data.downloadUrl.removePrefix("/")}"
                        val browserIntent = Intent(Intent.ACTION_VIEW, Uri.parse(downloadFullUrl))
                        context.startActivity(browserIntent)
                    }
                }
                is Resource.Error -> {
                    isExporting = false
                    Toast.makeText(context, res.message, Toast.LENGTH_LONG).show()
                }
                else -> {
                    isExporting = false
                }
            }
        }
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = {
            Column {
                Text("Export Sales Data (CSV)", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Text("Download detailed order spreadsheets for Excel", fontSize = 11.sp, color = TextSecondary)
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
                            text = "📊 What is CSV Export?",
                            fontSize = 12.sp,
                            fontWeight = FontWeight.Bold,
                            color = BrandOrange
                        )
                        Spacer(modifier = Modifier.height(2.dp))
                        Text(
                            text = "Exports full transaction logs, itemized order rows, revenue sums, and cashier billing logs into an Excel-ready spreadsheet.",
                            fontSize = 11.sp,
                            color = TextSecondary,
                            lineHeight = 14.sp
                        )
                    }
                }

                // Range Selector Buttons
                Text(text = "Choose Export Period:", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    OutlinedButton(
                        onClick = { exportType = "daily" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (exportType == "daily") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (exportType == "daily") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("📅 Today", fontSize = 12.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }

                    OutlinedButton(
                        onClick = { exportType = "monthly" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (exportType == "monthly") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (exportType == "monthly") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("📆 This Month", fontSize = 12.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }
                }

                if (exportType == "daily") {
                    OutlinedTextField(
                        value = selectedDate,
                        onValueChange = { selectedDate = it },
                        label = { Text("Date (YYYY-MM-DD)") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                } else if (exportType == "monthly") {
                    OutlinedTextField(
                        value = selectedMonth,
                        onValueChange = { selectedMonth = it },
                        label = { Text("Month (YYYY-MM)") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            }
        },
        confirmButton = {
            Button(
                onClick = { triggerExport(shareDirectly = true) },
                enabled = !isExporting,
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
            ) {
                if (isExporting) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                } else {
                    Icon(Icons.Default.Share, contentDescription = null, modifier = Modifier.size(16.dp))
                    Spacer(modifier = Modifier.width(6.dp))
                    Text("Export & Share CSV")
                }
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel")
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
