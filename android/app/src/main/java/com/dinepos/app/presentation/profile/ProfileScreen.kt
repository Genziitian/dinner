package com.dinepos.app.presentation.profile

import android.content.Intent
import android.net.Uri
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.filled.Logout
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
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*

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

    val username = remember { sessionManager.getUsername().ifBlank { "User" } }
    val role = remember { sessionManager.getUserRole().lowercase() }
    val userId = remember { sessionManager.getUserId() }
    val restaurantName = remember { sessionManager.getRestaurantName() }
    val restaurantAddress = remember { sessionManager.getRestaurantAddress() }
    val restaurantPhone = remember { sessionManager.getRestaurantPhone() }
    var currentBaseUrl by remember { mutableStateOf(sessionManager.getBaseUrl()) }

    var showServerConfigDialog by remember { mutableStateOf(false) }
    var showLogoutDialog by remember { mutableStateOf(false) }

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
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier.padding(horizontal = 14.dp, vertical = 6.dp)
                        ) {
                            Text(
                                text = roleTitle,
                                fontSize = 12.5.sp,
                                fontWeight = FontWeight.Bold,
                                color = roleBadgeColor
                            )
                        }
                    }

                    Spacer(modifier = Modifier.height(14.dp))
                    HorizontalDivider(color = BrandBorder.copy(alpha = 0.5f))
                    Spacer(modifier = Modifier.height(14.dp))

                    // Account Stats Row
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceEvenly
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = "User ID", fontSize = 11.sp, color = TextMuted, fontWeight = FontWeight.Medium)
                            Text(text = "#$userId", fontSize = 14.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                        }
                        Box(modifier = Modifier.height(28.dp).width(1.dp).background(BrandBorder))
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = "Account Status", fontSize = 11.sp, color = TextMuted, fontWeight = FontWeight.Medium)
                            Text(text = "🟢 Active", fontSize = 14.sp, fontWeight = FontWeight.Bold, color = StatusSuccess)
                        }
                        Box(modifier = Modifier.height(28.dp).width(1.dp).background(BrandBorder))
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(text = "Access Scope", fontSize = 11.sp, color = TextMuted, fontWeight = FontWeight.Medium)
                            Text(
                                text = if (role == "superadmin") "Global" else "Single Outlet",
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                        }
                    }
                }
            }

            // 2. Organization / Restaurant Outlet Details Card
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
                    Text(
                        text = "🏢 Organization & Outlet",
                        style = MaterialTheme.typography.titleSmall,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    if (role == "superadmin") {
                        InfoRow(
                            icon = Icons.Outlined.AdminPanelSettings,
                            label = "System Scope",
                            value = "Master Super Administrator (All Multi-Tenant Outlets)"
                        )
                        InfoRow(
                            icon = Icons.Outlined.Security,
                            label = "Security Guard",
                            value = "Strict Multi-Tenant Database Scoped Isolation"
                        )
                    } else {
                        InfoRow(
                            icon = Icons.Outlined.Storefront,
                            label = "Restaurant Outlet",
                            value = restaurantName.ifBlank { "Assigned Outlet" }
                        )
                        if (restaurantAddress.isNotBlank()) {
                            InfoRow(
                                icon = Icons.Outlined.LocationOn,
                                label = "Location",
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
                    }
                }
            }

            // 3. Server & Network Connection Card
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
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = "🌐 Connected Server",
                            style = MaterialTheme.typography.titleSmall,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        TextButton(
                            onClick = { showServerConfigDialog = true },
                            contentPadding = PaddingValues(horizontal = 8.dp, vertical = 2.dp)
                        ) {
                            Text("Switch Server", fontSize = 12.sp, color = BrandOrange, fontWeight = FontWeight.Bold)
                        }
                    }

                    Surface(
                        color = BrandBackground,
                        shape = RoundedCornerShape(10.dp),
                        border = BorderStroke(1.dp, BrandBorder)
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Icon(
                                imageVector = Icons.Default.CloudDone,
                                contentDescription = null,
                                tint = StatusSuccess,
                                modifier = Modifier.size(20.dp)
                            )
                            Spacer(modifier = Modifier.width(10.dp))
                            Column {
                                Text(
                                    text = "Main Production Server",
                                    fontSize = 11.sp,
                                    color = TextMuted,
                                    fontWeight = FontWeight.Medium
                                )
                                Text(
                                    text = currentBaseUrl,
                                    fontSize = 13.sp,
                                    fontWeight = FontWeight.SemiBold,
                                    color = BrandDark
                                )
                            }
                        }
                    }
                }
            }

            // 4. Role-Specific Action Shortcuts
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
                        text = "⚡ Quick Shortcuts",
                        style = MaterialTheme.typography.titleSmall,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    when (role) {
                        "superadmin" -> {
                            ProfileNavOption(
                                icon = Icons.Outlined.Assessment,
                                title = "Financial Reports & Analytics",
                                subtitle = "View daily, monthly & benchmark sales",
                                onClick = onNavigateToReports
                            )
                            ProfileNavOption(
                                icon = Icons.Outlined.Language,
                                title = "Open Web Super Admin Portal",
                                subtitle = "Manage restaurants, multi-tenancy & users",
                                onClick = {
                                    val intent = Intent(Intent.ACTION_VIEW, Uri.parse("${currentBaseUrl}admin/dashboard"))
                                    context.startActivity(intent)
                                }
                            )
                        }
                        "manager" -> {
                            ProfileNavOption(
                                icon = Icons.Outlined.Assessment,
                                title = "Financial Reports & Analytics",
                                subtitle = "View daily, monthly, and yearly sales",
                                onClick = onNavigateToReports
                            )
                            ProfileNavOption(
                                icon = Icons.Outlined.Receipt,
                                title = "Order History & Logs",
                                subtitle = "Search, filter & review orders",
                                onClick = onNavigateToOrders
                            )
                            ProfileNavOption(
                                icon = Icons.Outlined.RestaurantMenu,
                                title = "Manage Menu & Pricing",
                                subtitle = "Add dishes, portions & weight items",
                                onClick = onNavigateToItems
                            )
                        }
                        "cashier" -> {
                            ProfileNavOption(
                                icon = Icons.Outlined.ShoppingCart,
                                title = "POS Touch Billing Screen",
                                subtitle = "Take new order & print receipts",
                                onClick = onNavigateToBilling
                            )
                            ProfileNavOption(
                                icon = Icons.Outlined.Summarize,
                                title = "Today's Shift Summary",
                                subtitle = "Cash sales & total collected today",
                                onClick = onNavigateToSummary
                            )
                        }
                    }
                }
            }

            // 5. Legal & App Information
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

            // 6. Sign Out Button
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

    // Logout Confirmation Dialog
    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            title = {
                Text(text = "Sign Out", fontWeight = FontWeight.Bold)
            },
            text = {
                Text("Are you sure you want to sign out of your account on this device?")
            },
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

    // Server Config Dialog
    if (showServerConfigDialog) {
        var tempUrl by remember { mutableStateOf(currentBaseUrl) }

        AlertDialog(
            onDismissRequest = { showServerConfigDialog = false },
            title = {
                Text(text = "Server Configuration", fontWeight = FontWeight.Bold)
            },
            text = {
                Column {
                    Text(
                        text = "Enter DinePOS Backend Server URL:",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary
                    )
                    Spacer(modifier = Modifier.height(12.dp))
                    OutlinedTextField(
                        value = tempUrl,
                        onValueChange = { tempUrl = it },
                        singleLine = true,
                        label = { Text("Base URL") },
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        if (tempUrl.isNotBlank()) {
                            sessionManager.setBaseUrl(tempUrl)
                            DinePosApp.instance.initRepositories()
                            currentBaseUrl = sessionManager.getBaseUrl()
                            showServerConfigDialog = false
                            Toast.makeText(context, "Server URL updated!", Toast.LENGTH_SHORT).show()
                        }
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                ) {
                    Text("Save & Reconnect")
                }
            },
            dismissButton = {
                TextButton(onClick = { showServerConfigDialog = false }) {
                    Text("Cancel")
                }
            }
        )
    }
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
