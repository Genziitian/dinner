package com.dinepos.app.presentation.items

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ItemFormDialog(
    onDismiss: () -> Unit,
    onSave: (name: String, itemType: String, baseUnit: String, variants: List<Map<String, Any>>) -> Unit
) {
    var name by remember { mutableStateOf("") }
    var itemType by remember { mutableStateOf("portion") } // portion, piece, weight
    var baseUnit by remember { mutableStateOf("portion") }
    var validationError by remember { mutableStateOf<String?>(null) }

    // Portion variants state
    var quarterEnabled by remember { mutableStateOf(true) }
    var quarterPrice by remember { mutableStateOf("") }

    var halfEnabled by remember { mutableStateOf(true) }
    var halfPrice by remember { mutableStateOf("") }

    var fullEnabled by remember { mutableStateOf(true) }
    var fullPrice by remember { mutableStateOf("") }

    // Piece single/custom variants state
    var singlePiecePrice by remember { mutableStateOf("") }
    var hasCustomPiecePack by remember { mutableStateOf(false) }
    var customPiecePackName by remember { mutableStateOf("Plate (2 pcs)") }
    var customPieceQty by remember { mutableStateOf("2") }
    var customPiecePrice by remember { mutableStateOf("") }

    // Weight variants state
    var perKgPrice by remember { mutableStateOf("") }
    var enable250g by remember { mutableStateOf(true) }
    var enable500g by remember { mutableStateOf(true) }

    AlertDialog(
        onDismissRequest = onDismiss,
        modifier = Modifier.widthIn(max = 540.dp),
        title = {
            Column {
                Text("Add New Menu Item", fontWeight = FontWeight.ExtraBold, fontSize = 20.sp, color = BrandDark)
                Text("Configure dish details and portion pricing", fontSize = 12.sp, color = TextSecondary)
            }
        },
        text = {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                // Item Name
                OutlinedTextField(
                    value = name,
                    onValueChange = {
                        name = it
                        validationError = null
                    },
                    label = { Text("Item Name *") },
                    placeholder = { Text("e.g. Chicken Curry, Biryani, Naan") },
                    singleLine = true,
                    shape = RoundedCornerShape(12.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = BrandOrange,
                        focusedLabelColor = BrandOrange
                    ),
                    modifier = Modifier.fillMaxWidth()
                )

                // Pricing Type Selector (Segmented Tab Bar)
                Column {
                    Text("Pricing Type", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = BrandDark)
                    Spacer(modifier = Modifier.height(8.dp))

                    Surface(
                        shape = RoundedCornerShape(12.dp),
                        color = BrandBackground,
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(4.dp),
                            horizontalArrangement = Arrangement.spacedBy(4.dp)
                        ) {
                            TypeTab(
                                label = "🍗 Portion",
                                isSelected = itemType == "portion",
                                onClick = {
                                    itemType = "portion"
                                    baseUnit = "portion"
                                    validationError = null
                                },
                                modifier = Modifier.weight(1f)
                            )
                            TypeTab(
                                label = "🥚 Piece",
                                isSelected = itemType == "piece",
                                onClick = {
                                    itemType = "piece"
                                    baseUnit = "piece"
                                    validationError = null
                                },
                                modifier = Modifier.weight(1f)
                            )
                            TypeTab(
                                label = "⚖️ Weight",
                                isSelected = itemType == "weight",
                                onClick = {
                                    itemType = "weight"
                                    baseUnit = "kg"
                                    validationError = null
                                },
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }
                }

                HorizontalDivider(color = BrandBorder, thickness = 1.dp)

                // Dynamic Portion Matrix based on selected itemType
                when (itemType) {
                    "portion" -> {
                        Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            Text("Portion Sizes & Rates", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = BrandDark)

                            // 1. Quarter Portion
                            PortionRow(
                                title = "Quarter",
                                subtitle = "0.25 portion",
                                isChecked = quarterEnabled,
                                onCheckedChange = { quarterEnabled = it },
                                priceValue = quarterPrice,
                                onPriceChange = { quarterPrice = it; validationError = null }
                            )

                            // 2. Half Portion
                            PortionRow(
                                title = "Half",
                                subtitle = "0.50 portion",
                                isChecked = halfEnabled,
                                onCheckedChange = { halfEnabled = it },
                                priceValue = halfPrice,
                                onPriceChange = { halfPrice = it; validationError = null }
                            )

                            // 3. Full Portion
                            PortionRow(
                                title = "Full",
                                subtitle = "1.00 portion",
                                isChecked = fullEnabled,
                                onCheckedChange = { fullEnabled = it },
                                priceValue = fullPrice,
                                onPriceChange = { fullPrice = it; validationError = null }
                            )
                        }
                    }

                    "piece" -> {
                        Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            Text("Piece Pricing", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = BrandDark)

                            OutlinedTextField(
                                value = singlePiecePrice,
                                onValueChange = { singlePiecePrice = it; validationError = null },
                                label = { Text("Rate Per Piece *") },
                                placeholder = { Text("0.00") },
                                prefix = { Text("₹ ", fontWeight = FontWeight.Bold) },
                                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                                singleLine = true,
                                shape = RoundedCornerShape(12.dp),
                                modifier = Modifier.fillMaxWidth()
                            )

                            // Optional Multi-piece combo/plate
                            Surface(
                                shape = RoundedCornerShape(12.dp),
                                border = BorderStroke(1.dp, BrandBorder),
                                color = BrandBackground,
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                Column(modifier = Modifier.padding(12.dp)) {
                                    Row(
                                        modifier = Modifier.fillMaxWidth(),
                                        horizontalArrangement = Arrangement.SpaceBetween,
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Text("Add Bulk/Combo Pack", fontSize = 12.sp, fontWeight = FontWeight.SemiBold)
                                        Switch(
                                            checked = hasCustomPiecePack,
                                            onCheckedChange = { hasCustomPiecePack = it }
                                        )
                                    }

                                    if (hasCustomPiecePack) {
                                        Spacer(modifier = Modifier.height(8.dp))
                                        OutlinedTextField(
                                            value = customPiecePackName,
                                            onValueChange = { customPiecePackName = it },
                                            label = { Text("Pack Name") },
                                            singleLine = true,
                                            shape = RoundedCornerShape(8.dp),
                                            modifier = Modifier.fillMaxWidth()
                                        )
                                        Spacer(modifier = Modifier.height(6.dp))
                                        Row(
                                            modifier = Modifier.fillMaxWidth(),
                                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                                        ) {
                                            OutlinedTextField(
                                                value = customPieceQty,
                                                onValueChange = { customPieceQty = it },
                                                label = { Text("Qty (Pcs)") },
                                                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                                                singleLine = true,
                                                shape = RoundedCornerShape(8.dp),
                                                modifier = Modifier.weight(1f)
                                            )
                                            OutlinedTextField(
                                                value = customPiecePrice,
                                                onValueChange = { customPiecePrice = it },
                                                label = { Text("Price") },
                                                prefix = { Text("₹ ", fontWeight = FontWeight.Bold) },
                                                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                                                singleLine = true,
                                                shape = RoundedCornerShape(8.dp),
                                                modifier = Modifier.weight(1f)
                                            )
                                        }
                                    }
                                }
                            }
                        }
                    }

                    "weight" -> {
                        Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            Text("Weight Rates (Per Kg)", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = BrandDark)

                            OutlinedTextField(
                                value = perKgPrice,
                                onValueChange = {
                                    perKgPrice = it
                                    validationError = null
                                },
                                label = { Text("Base Rate Per Kg *") },
                                placeholder = { Text("0.00") },
                                prefix = { Text("₹ ", fontWeight = FontWeight.Bold) },
                                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                                singleLine = true,
                                shape = RoundedCornerShape(12.dp),
                                modifier = Modifier.fillMaxWidth()
                            )

                            Text(
                                text = "Auto-enabled portions for billing:",
                                fontSize = 11.sp,
                                color = TextSecondary,
                                fontWeight = FontWeight.SemiBold
                            )

                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(8.dp)
                            ) {
                                FilterChip(
                                    selected = enable250g,
                                    onClick = { enable250g = !enable250g },
                                    label = { Text("250g (1/4 kg)", fontWeight = FontWeight.SemiBold, fontSize = 12.sp) },
                                    colors = FilterChipDefaults.filterChipColors(
                                        selectedContainerColor = BrandOrange,
                                        selectedLabelColor = Color.White,
                                        containerColor = BrandBackground,
                                        labelColor = BrandDark
                                    ),
                                    border = FilterChipDefaults.filterChipBorder(
                                        enabled = true,
                                        selected = enable250g,
                                        borderColor = if (enable250g) BrandOrange else BrandBorder
                                    )
                                )
                                FilterChip(
                                    selected = enable500g,
                                    onClick = { enable500g = !enable500g },
                                    label = { Text("500g (1/2 kg)", fontWeight = FontWeight.SemiBold, fontSize = 12.sp) },
                                    colors = FilterChipDefaults.filterChipColors(
                                        selectedContainerColor = BrandOrange,
                                        selectedLabelColor = Color.White,
                                        containerColor = BrandBackground,
                                        labelColor = BrandDark
                                    ),
                                    border = FilterChipDefaults.filterChipBorder(
                                        enabled = true,
                                        selected = enable500g,
                                        borderColor = if (enable500g) BrandOrange else BrandBorder
                                    )
                                )
                            }
                        }
                    }
                }

                // Validation Error display
                if (!validationError.isNullOrBlank()) {
                    Surface(
                        color = Color(0x22EF4444),
                        shape = RoundedCornerShape(8.dp),
                        border = BorderStroke(1.dp, StatusError)
                    ) {
                        Text(
                            text = validationError ?: "",
                            color = StatusError,
                            fontSize = 12.sp,
                            fontWeight = FontWeight.SemiBold,
                            modifier = Modifier.padding(horizontal = 10.dp, vertical = 6.dp)
                        )
                    }
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (name.isBlank()) {
                        validationError = "Please enter an item name."
                        return@Button
                    }

                    val variantsList = mutableListOf<Map<String, Any>>()

                    when (itemType) {
                        "portion" -> {
                            if (quarterEnabled && (quarterPrice.toDoubleOrNull() ?: 0.0) > 0) {
                                variantsList.add(mapOf(
                                    "variant_name" to "Quarter",
                                    "quantity_value" to 0.25,
                                    "quantity_unit" to "portion",
                                    "price" to quarterPrice.toDouble()
                                ))
                            }
                            if (halfEnabled && (halfPrice.toDoubleOrNull() ?: 0.0) > 0) {
                                variantsList.add(mapOf(
                                    "variant_name" to "Half",
                                    "quantity_value" to 0.50,
                                    "quantity_unit" to "portion",
                                    "price" to halfPrice.toDouble()
                                ))
                            }
                            if (fullEnabled && (fullPrice.toDoubleOrNull() ?: 0.0) > 0) {
                                variantsList.add(mapOf(
                                    "variant_name" to "Full",
                                    "quantity_value" to 1.00,
                                    "quantity_unit" to "portion",
                                    "price" to fullPrice.toDouble()
                                ))
                            }
                        }

                        "piece" -> {
                            val price = singlePiecePrice.toDoubleOrNull() ?: 0.0
                            if (price > 0) {
                                variantsList.add(mapOf(
                                    "variant_name" to "Per Piece",
                                    "quantity_value" to 1.0,
                                    "quantity_unit" to "piece",
                                    "price" to price
                                ))
                            }
                            if (hasCustomPiecePack) {
                                val packQty = customPieceQty.toDoubleOrNull() ?: 2.0
                                val packPrice = customPiecePrice.toDoubleOrNull() ?: 0.0
                                if (packPrice > 0) {
                                    variantsList.add(mapOf(
                                        "variant_name" to customPiecePackName.ifBlank { "Pack" },
                                        "quantity_value" to packQty,
                                        "quantity_unit" to "piece",
                                        "price" to packPrice
                                    ))
                                }
                            }
                        }

                        "weight" -> {
                            val kgRate = perKgPrice.toDoubleOrNull() ?: 0.0
                            if (kgRate > 0) {
                                if (enable250g) {
                                    variantsList.add(mapOf(
                                        "variant_name" to "250g",
                                        "quantity_value" to 0.25,
                                        "quantity_unit" to "kg",
                                        "price" to (kgRate * 0.25)
                                    ))
                                }
                                if (enable500g) {
                                    variantsList.add(mapOf(
                                        "variant_name" to "500g",
                                        "quantity_value" to 0.50,
                                        "quantity_unit" to "kg",
                                        "price" to (kgRate * 0.50)
                                    ))
                                }
                                variantsList.add(mapOf(
                                    "variant_name" to "1 Kg",
                                    "quantity_value" to 1.00,
                                    "quantity_unit" to "kg",
                                    "price" to kgRate
                                ))
                            }
                        }
                    }

                    if (variantsList.isEmpty()) {
                        validationError = "Please enter at least one valid price for the item."
                        return@Button
                    }

                    onSave(name.trim(), itemType, baseUnit, variantsList)
                },
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Save Item", fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancel") }
        }
    )
}

@Composable
private fun TypeTab(
    label: String,
    isSelected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Surface(
        shape = RoundedCornerShape(8.dp),
        color = if (isSelected) BrandOrange else Color.Transparent,
        modifier = modifier
            .height(38.dp)
            .clickable { onClick() }
    ) {
        Box(contentAlignment = Alignment.Center, modifier = Modifier.fillMaxSize()) {
            Text(
                text = label,
                fontSize = 12.sp,
                fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
                color = if (isSelected) Color.White else BrandDark,
                textAlign = TextAlign.Center
            )
        }
    }
}

@Composable
private fun PortionRow(
    title: String,
    subtitle: String,
    isChecked: Boolean,
    onCheckedChange: (Boolean) -> Unit,
    priceValue: String,
    onPriceChange: (String) -> Unit
) {
    Surface(
        shape = RoundedCornerShape(12.dp),
        border = BorderStroke(1.dp, if (isChecked) BrandOrange.copy(alpha = 0.5f) else BrandBorder),
        color = if (isChecked) BrandOrangeLight.copy(alpha = 0.25f) else Color.Transparent,
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 10.dp, vertical = 8.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            Checkbox(
                checked = isChecked,
                onCheckedChange = onCheckedChange,
                colors = CheckboxDefaults.colors(checkedColor = BrandOrange)
            )

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = title,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp,
                    color = if (isChecked) BrandDark else TextSecondary
                )
                Text(
                    text = subtitle,
                    fontSize = 11.sp,
                    color = TextMuted
                )
            }

            OutlinedTextField(
                value = priceValue,
                onValueChange = onPriceChange,
                enabled = isChecked,
                placeholder = { Text("0.00", fontSize = 13.sp, color = TextMuted) },
                prefix = { Text("₹ ", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = if (isChecked) BrandDark else TextMuted) },
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                singleLine = true,
                shape = RoundedCornerShape(8.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = BrandOrange,
                    unfocusedContainerColor = Color.White,
                    focusedContainerColor = Color.White
                ),
                modifier = Modifier.width(115.dp)
            )
        }
    }
}
