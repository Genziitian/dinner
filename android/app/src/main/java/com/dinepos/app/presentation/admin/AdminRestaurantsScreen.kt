package com.dinepos.app.presentation.admin

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.*
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
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.AdminRestaurantDto
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminRestaurantsScreen() {
    val context = LocalContext.current
    val managerRepository = DinePosApp.instance.managerRepository
    val scope = rememberCoroutineScope()

    var restaurants by remember { mutableStateOf<List<AdminRestaurantDto>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var searchQuery by remember { mutableStateOf("") }

    var showCreateDialog by remember { mutableStateOf(false) }
    var editingRestaurant by remember { mutableStateOf<AdminRestaurantDto?>(null) }
    var isSubmitting by remember { mutableStateOf(false) }

    fun loadData() {
        scope.launch {
            isLoading = true
            when (val result = managerRepository.getAdminOverview()) {
                is Resource.Success -> {
                    restaurants = result.data.restaurants
                    isLoading = false
                }
                is Resource.Error -> {
                    isLoading = false
                    Toast.makeText(context, result.message, Toast.LENGTH_SHORT).show()
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

    val filteredRestaurants = remember(restaurants, searchQuery) {
        if (searchQuery.isBlank()) restaurants
        else restaurants.filter {
            it.name.contains(searchQuery, ignoreCase = true) ||
            (it.address?.contains(searchQuery, ignoreCase = true) == true) ||
            (it.phone?.contains(searchQuery, ignoreCase = true) == true)
        }
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text(
                            text = "Restaurant Management",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        Text(
                            text = "Manage multi-tenant outlets & branches",
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
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
            )
        },
        floatingActionButton = {
            FloatingActionButton(
                onClick = { showCreateDialog = true },
                containerColor = BrandOrange,
                contentColor = Color.White,
                shape = CircleShape
            ) {
                Icon(Icons.Default.Add, contentDescription = "Add Restaurant")
            }
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            // Search Bar
            OutlinedTextField(
                value = searchQuery,
                onValueChange = { searchQuery = it },
                placeholder = { Text("Search restaurant branches...", fontSize = 13.sp, color = TextMuted) },
                leadingIcon = { Icon(Icons.Default.Search, contentDescription = null, tint = TextSecondary) },
                singleLine = true,
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedContainerColor = Color.White,
                    unfocusedContainerColor = Color.White,
                    focusedBorderColor = BrandOrange,
                    unfocusedBorderColor = BrandBorder
                ),
                modifier = Modifier.fillMaxWidth()
            )

            // Header summary
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Outlets (${filteredRestaurants.size})",
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    color = BrandDark
                )
                Text(
                    text = "+ New Branch",
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    color = BrandOrange,
                    modifier = Modifier.clip(RoundedCornerShape(4.dp)).clickable { showCreateDialog = true }
                )
            }

            if (isLoading) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = BrandOrange)
                }
            } else if (filteredRestaurants.isEmpty()) {
                Box(
                    modifier = Modifier.fillMaxSize().padding(32.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(text = "🏪", fontSize = 42.sp)
                        Spacer(modifier = Modifier.height(10.dp))
                        Text(
                            text = "No Restaurant Outlets Found",
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Tap the '+' button below to create your first restaurant branch.",
                            fontSize = 12.sp,
                            color = TextSecondary,
                            textAlign = androidx.compose.ui.text.style.TextAlign.Center
                        )
                    }
                }
            } else {
                LazyColumn(
                    verticalArrangement = Arrangement.spacedBy(10.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    items(filteredRestaurants, key = { it.id }) { restaurant ->
                        RestaurantManagementCard(
                            restaurant = restaurant,
                            onEdit = { editingRestaurant = restaurant },
                            onToggleStatus = {
                                scope.launch {
                                    when (val res = managerRepository.toggleRestaurant(restaurant.id)) {
                                        is Resource.Success -> {
                                            Toast.makeText(context, "Status updated!", Toast.LENGTH_SHORT).show()
                                            loadData()
                                        }
                                        is Resource.Error -> {
                                            Toast.makeText(context, res.message, Toast.LENGTH_SHORT).show()
                                        }
                                        else -> {}
                                    }
                                }
                            }
                        )
                    }
                    item {
                        Spacer(modifier = Modifier.height(60.dp))
                    }
                }
            }
        }
    }

    // Create Restaurant Dialog
    if (showCreateDialog) {
        RestaurantFormDialog(
            title = "Create New Restaurant",
            initialName = "",
            initialPhone = "",
            initialAddress = "",
            isSubmitting = isSubmitting,
            onDismiss = { showCreateDialog = false },
            onConfirm = { name, phone, address ->
                scope.launch {
                    isSubmitting = true
                    when (val res = managerRepository.createRestaurant(name, phone, address)) {
                        is Resource.Success -> {
                            isSubmitting = false
                            showCreateDialog = false
                            Toast.makeText(context, "Restaurant '${res.data.name}' created!", Toast.LENGTH_SHORT).show()
                            loadData()
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
        )
    }

    // Edit Restaurant Dialog
    if (editingRestaurant != null) {
        val rest = editingRestaurant!!
        RestaurantFormDialog(
            title = "Edit Restaurant",
            initialName = rest.name,
            initialPhone = rest.phone ?: "",
            initialAddress = rest.address ?: "",
            isSubmitting = isSubmitting,
            onDismiss = { editingRestaurant = null },
            onConfirm = { name, phone, address ->
                scope.launch {
                    isSubmitting = true
                    when (val res = managerRepository.updateRestaurant(rest.id, name, phone, address, rest.timezone, rest.status)) {
                        is Resource.Success -> {
                            isSubmitting = false
                            editingRestaurant = null
                            Toast.makeText(context, "Restaurant updated successfully!", Toast.LENGTH_SHORT).show()
                            loadData()
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
        )
    }
}

@Composable
private fun RestaurantManagementCard(
    restaurant: AdminRestaurantDto,
    onEdit: () -> Unit,
    onToggleStatus: () -> Unit
) {
    val isActive = restaurant.status == "active"

    Card(
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.5.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier
                        .size(44.dp)
                        .clip(RoundedCornerShape(12.dp))
                        .background(BrandOrange.copy(alpha = 0.12f)),
                    contentAlignment = Alignment.Center
                ) {
                    Text(text = "🏪", fontSize = 22.sp)
                }

                Spacer(modifier = Modifier.width(12.dp))

                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = restaurant.name,
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Text(
                        text = "ID: #${restaurant.id} • ${restaurant.timezone}",
                        fontSize = 11.sp,
                        color = TextMuted
                    )
                }

                Surface(
                    color = if (isActive) Color(0x2210B981) else Color(0x22EF4444),
                    shape = RoundedCornerShape(50)
                ) {
                    Text(
                        text = if (isActive) "Active" else "Inactive",
                        color = if (isActive) StatusSuccess else StatusError,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp)
                    )
                }
            }

            if (!restaurant.address.isNullOrBlank() || !restaurant.phone.isNullOrBlank()) {
                Spacer(modifier = Modifier.height(10.dp))
                HorizontalDivider(color = BrandBorder.copy(alpha = 0.5f))
                Spacer(modifier = Modifier.height(8.dp))

                if (!restaurant.address.isNullOrBlank()) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Outlined.LocationOn, contentDescription = null, tint = TextMuted, modifier = Modifier.size(15.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(text = restaurant.address, fontSize = 12.sp, color = TextSecondary)
                    }
                }
                if (!restaurant.phone.isNullOrBlank()) {
                    Spacer(modifier = Modifier.height(4.dp))
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Outlined.Phone, contentDescription = null, tint = TextMuted, modifier = Modifier.size(15.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(text = restaurant.phone, fontSize = 12.sp, color = TextSecondary)
                    }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))

            // Card Action Buttons
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                OutlinedButton(
                    onClick = onEdit,
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.weight(1f)
                ) {
                    Icon(Icons.Default.Edit, contentDescription = "Edit", modifier = Modifier.size(16.dp), tint = BrandDark)
                    Spacer(modifier = Modifier.width(4.dp))
                    Text("Edit", fontSize = 12.sp, color = BrandDark, fontWeight = FontWeight.SemiBold)
                }

                Button(
                    onClick = onToggleStatus,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = if (isActive) StatusError.copy(alpha = 0.12f) else StatusSuccess.copy(alpha = 0.12f)
                    ),
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.weight(1f)
                ) {
                    Text(
                        text = if (isActive) "Deactivate" else "Activate",
                        fontSize = 12.sp,
                        color = if (isActive) StatusError else StatusSuccess,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}

@Composable
private fun RestaurantFormDialog(
    title: String,
    initialName: String,
    initialPhone: String,
    initialAddress: String,
    isSubmitting: Boolean,
    onDismiss: () -> Unit,
    onConfirm: (name: String, phone: String?, address: String?) -> Unit
) {
    var name by remember { mutableStateOf(initialName) }
    var phone by remember { mutableStateOf(initialPhone) }
    var address by remember { mutableStateOf(initialAddress) }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(text = title, fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Restaurant Name *") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = phone,
                    onValueChange = { phone = it },
                    label = { Text("Contact Phone") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = address,
                    onValueChange = { address = it },
                    label = { Text("Address") },
                    modifier = Modifier.fillMaxWidth()
                )
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (name.isNotBlank()) {
                        onConfirm(name.trim(), phone.trim().ifBlank { null }, address.trim().ifBlank { null })
                    }
                },
                enabled = name.isNotBlank() && !isSubmitting,
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
            ) {
                if (isSubmitting) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                } else {
                    Text("Save Outlet")
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
