package com.dinepos.app.presentation.mill

import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.dto.MillServiceDto
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MillServicesScreen(
    onNavigateBack: () -> Unit
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val millRepository = DinePosApp.instance.millRepository

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
    var isLoading by remember { mutableStateOf(false) }
    var showDialog by remember { mutableStateOf(false) }
    var editingService by remember { mutableStateOf<MillServiceDto?>(null) }

    fun loadServices() {
        scope.launch {
            isLoading = true
            when (val res = millRepository.getServices()) {
                is Resource.Success -> {
                    val list = res.data ?: emptyList()
                    services = if (list.isNotEmpty()) list else defaultServices
                }
                is Resource.Error -> {
                    services = defaultServices
                }
                else -> {}
            }
            isLoading = false
        }
    }

    LaunchedEffect(Unit) {
        loadServices()
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Grinding Rates",
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
                    IconButton(onClick = { loadServices() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh", tint = BrandDark)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandBackground)
            )
        },
        floatingActionButton = {
            FloatingActionButton(
                onClick = {
                    editingService = null
                    showDialog = true
                },
                containerColor = BrandOrange,
                contentColor = androidx.compose.ui.graphics.Color.White
            ) {
                Icon(Icons.Default.Add, contentDescription = "Add Service")
            }
        }
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
        ) {
            if (isLoading) {
                CircularProgressIndicator(
                    color = BrandOrange,
                    modifier = Modifier.align(Alignment.Center)
                )
            } else if (services.isEmpty()) {
                Text(
                    text = "No services added yet.",
                    color = TextSecondary,
                    modifier = Modifier.align(Alignment.Center)
                )
            } else {
                LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    items(services, key = { it.id }) { s ->
                        Card(
                            shape = RoundedCornerShape(14.dp),
                            colors = CardDefaults.cardColors(containerColor = BrandSurface),
                            border = BorderStroke(1.dp, BrandBorder),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(16.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(
                                        text = s.name,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 16.sp,
                                        color = BrandDark
                                    )
                                    if (!s.nameHi.isNullOrBlank()) {
                                        Text(
                                            text = s.nameHi,
                                            fontSize = 13.sp,
                                            color = TextSecondary
                                        )
                                    }
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = "${CurrencyFormatter.formatInr(s.ratePerKg)} / KG",
                                        fontWeight = FontWeight.ExtraBold,
                                        fontSize = 15.sp,
                                        color = BrandOrange
                                    )
                                }

                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    IconButton(onClick = {
                                        editingService = s
                                        showDialog = true
                                    }) {
                                        Icon(
                                            imageVector = Icons.Default.Edit,
                                            contentDescription = "Edit",
                                            tint = TextSecondary
                                        )
                                    }

                                    Switch(
                                        checked = s.active == 1,
                                        onCheckedChange = {
                                            scope.launch {
                                                millRepository.toggleService(s.id)
                                                loadServices()
                                            }
                                        },
                                        colors = SwitchDefaults.colors(checkedThumbColor = BrandOrange)
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }

        if (showDialog) {
            ServiceEditDialog(
                initial = editingService,
                onDismiss = { showDialog = false },
                onSave = { name, nameHi, rate ->
                    scope.launch {
                        val req = MillServiceDto(
                            id = editingService?.id ?: 0,
                            name = name,
                            nameHi = nameHi.ifBlank { null },
                            ratePerKg = rate,
                            active = editingService?.active ?: 1
                        )
                        when (val res = millRepository.saveService(req)) {
                            is Resource.Success -> {
                                Toast.makeText(context, "Service saved!", Toast.LENGTH_SHORT).show()
                                showDialog = false
                                loadServices()
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
    }
}

@Composable
private fun ServiceEditDialog(
    initial: MillServiceDto?,
    onDismiss: () -> Unit,
    onSave: (String, String, Double) -> Unit
) {
    var name by remember { mutableStateOf(initial?.name ?: "") }
    var nameHi by remember { mutableStateOf(initial?.nameHi ?: "") }
    var rateText by remember { mutableStateOf(initial?.ratePerKg?.toString() ?: "") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = {
            Text(
                text = if (initial == null) "Add Grinding Service" else "Edit Service",
                fontWeight = FontWeight.Bold
            )
        },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Service Name (English) *") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = nameHi,
                    onValueChange = { nameHi = it },
                    label = { Text("Service Name (Hindi)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = rateText,
                    onValueChange = { rateText = it },
                    label = { Text("Rate per KG (Rs.) *") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    val rate = rateText.toDoubleOrNull() ?: 0.0
                    if (name.isBlank() || rate <= 0) return@Button
                    onSave(name.trim(), nameHi.trim(), rate)
                },
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
            ) {
                Text("Save")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel")
            }
        }
    )
}
