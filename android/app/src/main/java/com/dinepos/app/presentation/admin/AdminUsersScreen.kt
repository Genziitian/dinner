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
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.AdminRestaurantDto
import com.dinepos.app.data.dto.AdminUserDto
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AdminUsersScreen() {
    val context = LocalContext.current
    val managerRepository = DinePosApp.instance.managerRepository
    val sessionManager = DinePosApp.instance.sessionManager
    val scope = rememberCoroutineScope()

    var users by remember { mutableStateOf<List<AdminUserDto>>(emptyList()) }
    var restaurants by remember { mutableStateOf<List<AdminRestaurantDto>>(emptyList()) }
    var isLoading by remember { mutableStateOf(true) }
    var searchQuery by remember { mutableStateOf("") }
    var selectedRoleFilter by remember { mutableStateOf("all") }

    var showCreateDialog by remember { mutableStateOf(false) }
    var editingUser by remember { mutableStateOf<AdminUserDto?>(null) }
    var isSubmitting by remember { mutableStateOf(false) }

    fun loadData() {
        scope.launch {
            isLoading = true
            when (val result = managerRepository.getAdminOverview()) {
                is Resource.Success -> {
                    users = result.data.users
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

    val filteredUsers = remember(users, searchQuery, selectedRoleFilter) {
        users.filter { user ->
            val matchesRole = when (selectedRoleFilter) {
                "manager" -> user.role == "manager"
                "cashier" -> user.role == "cashier"
                "superadmin" -> user.role == "superadmin"
                else -> true
            }
            val matchesSearch = searchQuery.isBlank() ||
                user.username.contains(searchQuery, ignoreCase = true) ||
                (user.restaurantName?.contains(searchQuery, ignoreCase = true) == true)

            matchesRole && matchesSearch
        }
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text(
                            text = "User Administration",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        Text(
                            text = "Create, assign & reset passwords for managers & staff",
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
                Icon(Icons.Default.PersonAdd, contentDescription = "Add User")
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
                placeholder = { Text("Search users by name or outlet...", fontSize = 13.sp, color = TextMuted) },
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

            // Role Filter Chips
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                FilterChip(
                    selected = selectedRoleFilter == "all",
                    onClick = { selectedRoleFilter = "all" },
                    label = { Text("All (${users.size})", fontSize = 11.5.sp) }
                )
                FilterChip(
                    selected = selectedRoleFilter == "manager",
                    onClick = { selectedRoleFilter = "manager" },
                    label = { Text("👔 Managers", fontSize = 11.5.sp) }
                )
                FilterChip(
                    selected = selectedRoleFilter == "cashier",
                    onClick = { selectedRoleFilter = "cashier" },
                    label = { Text("🛒 Cashiers", fontSize = 11.5.sp) }
                )
                FilterChip(
                    selected = selectedRoleFilter == "superadmin",
                    onClick = { selectedRoleFilter = "superadmin" },
                    label = { Text("👑 Admins", fontSize = 11.5.sp) }
                )
            }

            // Summary row
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = "Staff & Manager Accounts (${filteredUsers.size})",
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    color = BrandDark
                )
                Text(
                    text = "+ New User",
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
            } else if (filteredUsers.isEmpty()) {
                Box(
                    modifier = Modifier.fillMaxSize().padding(32.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(text = "👥", fontSize = 42.sp)
                        Spacer(modifier = Modifier.height(10.dp))
                        Text(
                            text = "No Users Found",
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Tap '+ New User' to create a new Restaurant Manager or Cashier user.",
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
                    items(filteredUsers, key = { it.id }) { userItem ->
                        UserManagementCard(
                            userItem = userItem,
                            currentUserId = sessionManager.getUserId(),
                            onEdit = { editingUser = userItem },
                            onToggleStatus = {
                                scope.launch {
                                    when (val res = managerRepository.toggleAdminUser(userItem.id)) {
                                        is Resource.Success -> {
                                            Toast.makeText(context, "User status updated!", Toast.LENGTH_SHORT).show()
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

    // Create User Dialog (Super Admin)
    if (showCreateDialog) {
        CreateUserDialog(
            restaurants = restaurants,
            isSubmitting = isSubmitting,
            onDismiss = { showCreateDialog = false },
            onConfirm = { username, password, role, restaurantId ->
                scope.launch {
                    isSubmitting = true
                    when (val res = managerRepository.createAdminUser(username, password, role, restaurantId)) {
                        is Resource.Success -> {
                            isSubmitting = false
                            showCreateDialog = false
                            Toast.makeText(context, "User '${res.data.username}' created successfully!", Toast.LENGTH_SHORT).show()
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

    // Edit User Dialog (Super Admin)
    if (editingUser != null) {
        val userToEdit = editingUser!!
        EditUserDialog(
            user = userToEdit,
            restaurants = restaurants,
            isSubmitting = isSubmitting,
            onDismiss = { editingUser = null },
            onConfirm = { username, password, role, restaurantId, status ->
                scope.launch {
                    isSubmitting = true
                    when (val res = managerRepository.updateAdminUser(userToEdit.id, username, password, role, restaurantId, status)) {
                        is Resource.Success -> {
                            isSubmitting = false
                            editingUser = null
                            Toast.makeText(context, "User '${res.data.username}' updated successfully!", Toast.LENGTH_SHORT).show()
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
private fun UserManagementCard(
    userItem: AdminUserDto,
    currentUserId: Int,
    onEdit: () -> Unit,
    onToggleStatus: () -> Unit
) {
    val isActive = userItem.status == "active"
    val (roleTitle, roleColor, roleIcon) = when (userItem.role) {
        "superadmin" -> Triple("Super Administrator", Color(0xFFF59E0B), "👑")
        "manager" -> Triple("Restaurant Manager", Color(0xFF3B82F6), "👔")
        "cashier" -> Triple("Cashier", Color(0xFF10B981), "🛒")
        else -> Triple("Staff", TextSecondary, "👤")
    }

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
                        .clip(CircleShape)
                        .background(roleColor.copy(alpha = 0.12f)),
                    contentAlignment = Alignment.Center
                ) {
                    Text(text = roleIcon, fontSize = 20.sp)
                }

                Spacer(modifier = Modifier.width(12.dp))

                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = userItem.username,
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Text(
                        text = roleTitle,
                        fontSize = 12.sp,
                        fontWeight = FontWeight.SemiBold,
                        color = roleColor
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

            Spacer(modifier = Modifier.height(10.dp))
            HorizontalDivider(color = BrandBorder.copy(alpha = 0.5f))
            Spacer(modifier = Modifier.height(8.dp))

            // Assigned Outlet Info
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(Icons.Outlined.Storefront, contentDescription = null, tint = TextMuted, modifier = Modifier.size(15.dp))
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = if (userItem.role == "superadmin") "Global System Access (All Outlets)"
                           else "Outlet: ${userItem.restaurantName ?: "Not Assigned"}",
                    fontSize = 12.sp,
                    color = TextSecondary,
                    fontWeight = FontWeight.Medium
                )
            }

            Spacer(modifier = Modifier.height(12.dp))

            // Action Buttons
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                OutlinedButton(
                    onClick = onEdit,
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.weight(1f)
                ) {
                    Icon(Icons.Default.Edit, contentDescription = "Edit", modifier = Modifier.size(15.dp), tint = BrandDark)
                    Spacer(modifier = Modifier.width(4.dp))
                    Text("Edit / Reset", fontSize = 12.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                }

                if (userItem.id != currentUserId) {
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
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun CreateUserDialog(
    restaurants: List<AdminRestaurantDto>,
    isSubmitting: Boolean,
    onDismiss: () -> Unit,
    onConfirm: (username: String, password: String, role: String, restaurantId: Int?) -> Unit
) {
    var username by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var selectedRole by remember { mutableStateOf("manager") }
    var selectedRestaurantId by remember { mutableStateOf(restaurants.firstOrNull()?.id) }
    var restaurantDropdownExpanded by remember { mutableStateOf(false) }

    val selectedRestaurantName = restaurants.find { it.id == selectedRestaurantId }?.name ?: "Select Restaurant"
    val passwordsMatch = password.isNotEmpty() && password == confirmPassword

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(text = "Create New User (Super Admin)", fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                OutlinedTextField(
                    value = username,
                    onValueChange = { username = it.lowercase().trim() },
                    label = { Text("Username (3-10 chars) *") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )

                OutlinedTextField(
                    value = password,
                    onValueChange = { password = it },
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
                            if (passwordsMatch) Text("🟢 Passwords match", color = StatusSuccess, fontSize = 11.sp)
                            else Text("❌ Passwords do not match", color = StatusError, fontSize = 11.sp)
                        }
                    },
                    modifier = Modifier.fillMaxWidth()
                )

                // Role Radio Selection
                Text(text = "Assign Role:", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    OutlinedButton(
                        onClick = { selectedRole = "manager" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (selectedRole == "manager") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (selectedRole == "manager") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("👔 Manager", fontSize = 12.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }

                    OutlinedButton(
                        onClick = { selectedRole = "cashier" },
                        colors = ButtonDefaults.outlinedButtonColors(
                            containerColor = if (selectedRole == "cashier") BrandOrange.copy(alpha = 0.15f) else Color.Transparent
                        ),
                        border = BorderStroke(1.dp, if (selectedRole == "cashier") BrandOrange else BrandBorder),
                        shape = RoundedCornerShape(10.dp),
                        modifier = Modifier.weight(1f)
                    ) {
                        Text("🛒 Cashier", fontSize = 12.sp, color = BrandDark, fontWeight = FontWeight.Bold)
                    }
                }

                // Assigned Restaurant Selector
                if (selectedRole != "superadmin") {
                    Text(text = "Assign to Restaurant Outlet:", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                    ExposedDropdownMenuBox(
                        expanded = restaurantDropdownExpanded,
                        onExpandedChange = { restaurantDropdownExpanded = !restaurantDropdownExpanded }
                    ) {
                        OutlinedTextField(
                            value = selectedRestaurantName,
                            onValueChange = {},
                            readOnly = true,
                            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = restaurantDropdownExpanded) },
                            modifier = Modifier.menuAnchor().fillMaxWidth()
                        )
                        ExposedDropdownMenu(
                            expanded = restaurantDropdownExpanded,
                            onDismissRequest = { restaurantDropdownExpanded = false }
                        ) {
                            restaurants.forEach { rest ->
                                DropdownMenuItem(
                                    text = { Text(rest.name) },
                                    onClick = {
                                        selectedRestaurantId = rest.id
                                        restaurantDropdownExpanded = false
                                    }
                                )
                            }
                        }
                    }
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (username.isNotBlank() && passwordsMatch) {
                        onConfirm(username, password, selectedRole, selectedRestaurantId)
                    }
                },
                enabled = username.isNotBlank() && passwordsMatch && password.length >= 8 && !isSubmitting,
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
            ) {
                if (isSubmitting) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                } else {
                    Text("Create User")
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

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun EditUserDialog(
    user: AdminUserDto,
    restaurants: List<AdminRestaurantDto>,
    isSubmitting: Boolean,
    onDismiss: () -> Unit,
    onConfirm: (username: String, password: String, role: String, restaurantId: Int?, status: String) -> Unit
) {
    var username by remember { mutableStateOf(user.username) }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var selectedRole by remember { mutableStateOf(user.role) }
    var selectedRestaurantId by remember { mutableStateOf(user.restaurantId ?: restaurants.firstOrNull()?.id) }
    var restaurantDropdownExpanded by remember { mutableStateOf(false) }

    val selectedRestaurantName = restaurants.find { it.id == selectedRestaurantId }?.name ?: "Select Restaurant"
    val passwordsValid = password.isEmpty() || (password == confirmPassword && password.length >= 8)

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(text = "Edit User: ${user.username}", fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                OutlinedTextField(
                    value = username,
                    onValueChange = { username = it.lowercase().trim() },
                    label = { Text("Username *") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )

                Text(text = "Reset Password (leave blank to keep existing):", fontSize = 11.5.sp, color = TextMuted)
                OutlinedTextField(
                    value = password,
                    onValueChange = { password = it },
                    label = { Text("New Password") },
                    singleLine = true,
                    visualTransformation = PasswordVisualTransformation(),
                    modifier = Modifier.fillMaxWidth()
                )

                if (password.isNotEmpty()) {
                    OutlinedTextField(
                        value = confirmPassword,
                        onValueChange = { confirmPassword = it },
                        label = { Text("Confirm New Password") },
                        singleLine = true,
                        visualTransformation = PasswordVisualTransformation(),
                        isError = password != confirmPassword,
                        modifier = Modifier.fillMaxWidth()
                    )
                }

                // Assigned Restaurant Selector
                if (selectedRole != "superadmin") {
                    Text(text = "Assigned Restaurant Outlet:", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                    ExposedDropdownMenuBox(
                        expanded = restaurantDropdownExpanded,
                        onExpandedChange = { restaurantDropdownExpanded = !restaurantDropdownExpanded }
                    ) {
                        OutlinedTextField(
                            value = selectedRestaurantName,
                            onValueChange = {},
                            readOnly = true,
                            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = restaurantDropdownExpanded) },
                            modifier = Modifier.menuAnchor().fillMaxWidth()
                        )
                        ExposedDropdownMenu(
                            expanded = restaurantDropdownExpanded,
                            onDismissRequest = { restaurantDropdownExpanded = false }
                        ) {
                            restaurants.forEach { rest ->
                                DropdownMenuItem(
                                    text = { Text(rest.name) },
                                    onClick = {
                                        selectedRestaurantId = rest.id
                                        restaurantDropdownExpanded = false
                                    }
                                )
                            }
                        }
                    }
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (username.isNotBlank() && passwordsValid) {
                        onConfirm(username, password, selectedRole, selectedRestaurantId, user.status)
                    }
                },
                enabled = username.isNotBlank() && passwordsValid && !isSubmitting,
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
            ) {
                if (isSubmitting) {
                    CircularProgressIndicator(color = Color.White, modifier = Modifier.size(16.dp))
                } else {
                    Text("Save Changes")
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
