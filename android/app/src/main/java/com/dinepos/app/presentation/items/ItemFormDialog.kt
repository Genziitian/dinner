package com.dinepos.app.presentation.items

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import com.dinepos.app.core.theme.BrandOrange

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ItemFormDialog(
    onDismiss: () -> Unit,
    onSave: (name: String, itemType: String, baseUnit: String, price: Double) -> Unit
) {
    var name by remember { mutableStateOf("") }
    var itemType by remember { mutableStateOf("piece") } // piece, portion, weight
    var baseUnit by remember { mutableStateOf("piece") }
    var priceText by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Add Menu Item", fontWeight = FontWeight.Bold) },
        text = {
            Column(
                modifier = Modifier.fillMaxWidth(),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Item Name (e.g. Chicken Biryani)") },
                    singleLine = true,
                    shape = RoundedCornerShape(12.dp),
                    modifier = Modifier.fillMaxWidth()
                )

                // Item Type Selector
                Text("Item Type:", style = MaterialTheme.typography.bodySmall, fontWeight = FontWeight.Bold)
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(6.dp)
                ) {
                    listOf("piece" to "Piece", "portion" to "Portion", "weight" to "Weight").forEach { (typeKey, typeLabel) ->
                        FilterChip(
                            selected = itemType == typeKey,
                            onClick = {
                                itemType = typeKey
                                baseUnit = when (typeKey) {
                                    "weight" -> "kg"
                                    "portion" -> "portion"
                                    else -> "piece"
                                }
                            },
                            label = { Text(typeLabel) }
                        )
                    }
                }

                OutlinedTextField(
                    value = priceText,
                    onValueChange = { priceText = it },
                    label = { Text("Rate / Price (₹)") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                    singleLine = true,
                    shape = RoundedCornerShape(12.dp),
                    modifier = Modifier.fillMaxWidth()
                )
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    val price = priceText.toDoubleOrNull() ?: 0.0
                    if (name.isNotBlank() && price > 0) {
                        onSave(name.trim(), itemType, baseUnit, price)
                    }
                },
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Create Item")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancel") }
        }
    )
}
