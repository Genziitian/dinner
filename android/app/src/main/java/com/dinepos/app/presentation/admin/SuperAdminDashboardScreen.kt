package com.dinepos.app.presentation.admin

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Logout
import androidx.compose.material.icons.automirrored.filled.OpenInNew
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.AdminOverviewResponseDto
import com.dinepos.app.data.dto.AdminRestaurantDto
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SuperAdminDashboardScreen(
    onNavigateToRestaurants: () -> Unit,
    onNavigateToUsers: () -> Unit,
    onNavigateToReports: () -> Unit,
    onNavigateToProfile: () -> Unit,
    onLogout: () -> Unit
) {
    val context = LocalContext.current
    val sessionManager = DinePosApp.instance.sessionManager
    val managerRepository = DinePosApp.instance.managerRepository
    val scope = rememberCoroutineScope()

    var overview by remember { mutableStateOf<AdminOverviewResponseDto?>(null) }
    var isLoading by remember { mutableStateOf(true) }
    var errorMessage by remember { mutableStateOf<String?>(null) }
    var showLogoutDialog by remember { mutableStateOf(false) }

    val username = remember { sessionManager.getUsername().ifBlank { "Super Admin" } }
    val baseUrl = remember { sessionManager.getBaseUrl() }

    fun loadData() {
        scope.launch {
            isLoading = true
            errorMessage = null
            when (val result = managerRepository.getAdminOverview()) {
                is Resource.Success -> {
                    overview = result.data
                    isLoading = false
                }
                is Resource.Error -> {
                    errorMessage = result.message
                    isLoading = false
                }
                else -> {
                    isLoading = false
                }
            }
        }
    }

    LaunchedEffect(Unit) {
        loadData()
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text(text = "👑 ", fontSize = 14.sp)
                            Text(
                                text = "Super Admin Console",
                                style = MaterialTheme.typography.titleMedium,
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                        }
                        Text(
                            text = "Multi-Tenant Master Control",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextSecondary,
                            fontSize = 11.sp
                        )
                    }
                },
                actions = {
                    IconButton(onClick = { loadData() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = BrandDark)
                    }
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
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // 1. Admin Master Hero Banner
            item {
                Card(
                    shape = RoundedCornerShape(20.dp),
                    colors = CardDefaults.cardColors(containerColor = BrandDark),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(
                                Brush.horizontalGradient(
                                    colors = listOf(BrandDark, Color(0xFF1E293B))
                                )
                            )
                            .padding(20.dp)
                    ) {
                        Column {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Surface(
                                    color = BrandOrange.copy(alpha = 0.2f),
                                    shape = RoundedCornerShape(50),
                                    border = BorderStroke(1.dp, BrandOrange.copy(alpha = 0.4f))
                                ) {
                                    Text(
                                        text = "👑 System Owner",
                                        color = BrandOrange,
                                        fontSize = 11.sp,
                                        fontWeight = FontWeight.Bold,
                                        modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp)
                                    )
                                }

                                Surface(
                                    color = Color(0x3310B981),
                                    shape = RoundedCornerShape(50)
                                ) {
                                    Text(
                                        text = "🟢 Live Connected",
                                        color = Color(0xFF34D399),
                                        fontSize = 11.sp,
                                        fontWeight = FontWeight.SemiBold,
                                        modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp)
                                    )
                                }
                            }

                            Spacer(modifier = Modifier.height(14.dp))

                            Text(
                                text = "Welcome, $username",
                                fontSize = 20.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color.White
                            )

                            Text(
                                text = "Manage multi-tenant restaurant branches, assign managers & cashiers, and view global analytics.",
                                fontSize = 12.sp,
                                color = TextMuted,
                                lineHeight = 16.sp
                            )
                        }
                    }
                }
            }

            // 2. High-Level Metrics Grid
            item {
                val totalRest = overview?.totalRestaurants ?: 0
                val totalUsers = overview?.totalUsers ?: 0

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    AdminMetricCard(
                        icon = Icons.Outlined.Storefront,
                        title = "Outlets",
                        value = if (isLoading) "..." else "$totalRest",
                        subtitle = "Manage Outlets",
                        iconColor = BrandOrange,
                        onClick = onNavigateToRestaurants,
                        modifier = Modifier.weight(1f)
                    )

                    AdminMetricCard(
                        icon = Icons.Outlined.People,
                        title = "Staff",
                        value = if (isLoading) "..." else "$totalUsers",
                        subtitle = "Managers & Cashiers",
                        iconColor = Color(0xFF3B82F6),
                        onClick = onNavigateToUsers,
                        modifier = Modifier.weight(1f)
                    )
                }
            }

            // 3. Super Admin Primary Control Modules
            item {
                Text(
                    text = "Core Administration Modules",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold,
                    color = BrandDark
                )
            }

            item {
                Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        AdminActionTile(
                            icon = Icons.Outlined.Storefront,
                            title = "Restaurants",
                            subtitle = "Create & edit branches",
                            color = BrandOrange,
                            onClick = onNavigateToRestaurants,
                            modifier = Modifier.weight(1f)
                        )
                        AdminActionTile(
                            icon = Icons.Outlined.People,
                            title = "User Administration",
                            subtitle = "Assign managers & cashiers",
                            color = Color(0xFF3B82F6),
                            onClick = onNavigateToUsers,
                            modifier = Modifier.weight(1f)
                        )
                    }

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        AdminActionTile(
                            icon = Icons.Outlined.Assessment,
                            title = "Global Reports",
                            subtitle = "Financial sales analytics",
                            color = Color(0xFF10B981),
                            onClick = onNavigateToReports,
                            modifier = Modifier.weight(1f)
                        )
                        AdminActionTile(
                            icon = Icons.Outlined.Person,
                            title = "Admin Profile",
                            subtitle = "Server & security details",
                            color = Color(0xFF8B5CF6),
                            onClick = onNavigateToProfile,
                            modifier = Modifier.weight(1f)
                        )
                    }
                }
            }

            // 4. Web Super Admin Portal Link
            item {
                Card(
                    shape = RoundedCornerShape(16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    border = BorderStroke(1.dp, BrandOrange.copy(alpha = 0.3f)),
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable {
                            val intent = Intent(Intent.ACTION_VIEW, Uri.parse("${baseUrl}admin/dashboard"))
                            context.startActivity(intent)
                        }
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(16.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Box(
                            modifier = Modifier
                                .size(44.dp)
                                .clip(RoundedCornerShape(12.dp))
                                .background(BrandOrange.copy(alpha = 0.1f)),
                            contentAlignment = Alignment.Center
                        ) {
                            Icon(Icons.Outlined.Language, contentDescription = null, tint = BrandOrange)
                        }
                        Spacer(modifier = Modifier.width(14.dp))
                        Column(modifier = Modifier.weight(1f)) {
                            Text(
                                text = "Open Web Super Admin Portal",
                                fontWeight = FontWeight.Bold,
                                fontSize = 14.sp,
                                color = BrandDark
                            )
                            Text(
                                text = "Full web browser console for database management",
                                fontSize = 12.sp,
                                color = TextSecondary
                            )
                        }
                        Icon(Icons.AutoMirrored.Filled.OpenInNew, contentDescription = null, tint = BrandOrange)
                    }
                }
            }

            // 5. Registered Restaurants Preview
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = "Registered Outlets",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    TextButton(onClick = onNavigateToRestaurants) {
                        Text("View All", fontSize = 12.sp, color = BrandOrange, fontWeight = FontWeight.Bold)
                    }
                }
            }

            val restaurants = overview?.restaurants ?: emptyList()
            if (isLoading) {
                item {
                    Box(modifier = Modifier.fillMaxWidth().padding(24.dp), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = BrandOrange)
                    }
                }
            } else if (restaurants.isEmpty()) {
                item {
                    Card(
                        shape = RoundedCornerShape(14.dp),
                        colors = CardDefaults.cardColors(containerColor = Color.White),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(
                            modifier = Modifier.fillMaxWidth().padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text(text = "🏪", fontSize = 32.sp)
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                text = "No Restaurants Created Yet",
                                fontWeight = FontWeight.Bold,
                                color = BrandDark
                            )
                            Text(
                                text = "Go to the Restaurants tab to add your first outlet branch.",
                                fontSize = 12.sp,
                                color = TextSecondary
                            )
                        }
                    }
                }
            } else {
                items(restaurants.take(5)) { rest ->
                    RestaurantCardItem(restaurant = rest)
                }
            }

            item {
                Spacer(modifier = Modifier.height(60.dp))
            }
        }
    }

    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            title = { Text(text = "Sign Out", fontWeight = FontWeight.Bold) },
            text = { Text("Are you sure you want to sign out from the Super Admin Console?") },
            confirmButton = {
                Button(
                    onClick = {
                        showLogoutDialog = false
                        sessionManager.clearSession()
                        onLogout()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = StatusError)
                ) {
                    Text("Sign Out")
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

@Composable
private fun AdminMetricCard(
    icon: ImageVector,
    title: String,
    value: String,
    subtitle: String,
    iconColor: Color,
    onClick: () -> Unit = {},
    modifier: Modifier = Modifier
) {
    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
        modifier = modifier.clickable { onClick() }
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Box(
                modifier = Modifier
                    .size(36.dp)
                    .clip(RoundedCornerShape(10.dp))
                    .background(iconColor.copy(alpha = 0.12f)),
                contentAlignment = Alignment.Center
            ) {
                Icon(imageVector = icon, contentDescription = null, tint = iconColor, modifier = Modifier.size(20.dp))
            }
            Spacer(modifier = Modifier.height(10.dp))
            Text(text = value, fontSize = 22.sp, fontWeight = FontWeight.Bold, color = BrandDark)
            Text(text = title, fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
            Text(text = subtitle, fontSize = 11.sp, color = TextSecondary)
        }
    }
}

@Composable
private fun AdminActionTile(
    icon: ImageVector,
    title: String,
    subtitle: String,
    color: Color,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
        modifier = modifier.clickable { onClick() }
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            Box(
                modifier = Modifier
                    .size(34.dp)
                    .clip(RoundedCornerShape(10.dp))
                    .background(color.copy(alpha = 0.12f)),
                contentAlignment = Alignment.Center
            ) {
                Icon(imageVector = icon, contentDescription = null, tint = color, modifier = Modifier.size(18.dp))
            }
            Spacer(modifier = Modifier.height(10.dp))
            Text(text = title, fontSize = 13.5.sp, fontWeight = FontWeight.Bold, color = BrandDark)
            Text(text = subtitle, fontSize = 11.sp, color = TextSecondary)
        }
    }
}

@Composable
private fun RestaurantCardItem(restaurant: AdminRestaurantDto) {
    Card(
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(42.dp)
                    .clip(RoundedCornerShape(12.dp))
                    .background(BrandOrange.copy(alpha = 0.12f)),
                contentAlignment = Alignment.Center
            ) {
                Text(text = "🏪", fontSize = 20.sp)
            }
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = restaurant.name,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp,
                    color = BrandDark
                )
                if (!restaurant.address.isNullOrBlank()) {
                    Text(
                        text = restaurant.address,
                        fontSize = 11.5.sp,
                        color = TextSecondary
                    )
                }
                if (!restaurant.phone.isNullOrBlank()) {
                    Text(
                        text = "📞 ${restaurant.phone}",
                        fontSize = 11.sp,
                        color = TextMuted
                    )
                }
            }
            Surface(
                color = if (restaurant.status == "active") Color(0x2210B981) else Color(0x22EF4444),
                shape = RoundedCornerShape(50)
            ) {
                Text(
                    text = if (restaurant.status == "active") "Active" else "Inactive",
                    color = if (restaurant.status == "active") StatusSuccess else StatusError,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                )
            }
        }
    }
}
