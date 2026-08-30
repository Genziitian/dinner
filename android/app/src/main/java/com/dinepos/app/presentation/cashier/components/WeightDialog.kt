package com.dinepos.app.presentation.cashier.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.ItemVariant
import com.dinepos.app.domain.model.MenuItem

@Composable
fun WeightDialog(
    item: MenuItem,
    variant: ItemVariant,
    onDismiss: () -> Unit,
    onConfirm: (quantity: Double) -> Unit
) {
    var weightText by remember { mutableStateOf("1.0") }
    val unit = variant.quantityUnit.ifBlank { item.baseUnit }

    val currentQty = weightText.toDoubleOrNull() ?: 0.0
    val totalAmount = currentQty * variant.price

    AlertDialog(
        onDismissRequest = onDismiss,
        title = {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text(text = "🌾 ", fontSize = 22.sp)
                Text(text = item.name, fontWeight = FontWeight.Bold)
            }
        },
        text = {
            Column(modifier = Modifier.fillMaxWidth()) {
                Surface(
                    shape = RoundedCornerShape(12.dp),
                    color = BrandEmeraldLight,
                    border = BorderStroke(1.dp, BrandEmerald),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(
                        modifier = Modifier.padding(12.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(text = "Authoritative Rate:", color = TextSecondary, fontWeight = FontWeight.SemiBold)
                        Text(
                            text = "${CurrencyFormatter.formatInr(variant.price)} / $unit",
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                OutlinedTextField(
                    value = weightText,
                    onValueChange = { weightText = it },
                    label = { Text("Quantity in ${unit.uppercase()}") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                    singleLine = true,
                    shape = RoundedCornerShape(12.dp),
                    modifier = Modifier.fillMaxWidth()
                )

                Spacer(modifier = Modifier.height(12.dp))

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(6.dp)
                ) {
                    listOf(0.25, 0.5, 1.0, 2.0).forEach { preset ->
                        OutlinedButton(
                            onClick = { weightText = preset.toString() },
                            shape = RoundedCornerShape(8.dp),
                            contentPadding = PaddingValues(horizontal = 8.dp, vertical = 4.dp),
                            modifier = Modifier.weight(1f)
                        ) {
                            Text(text = "$preset", fontSize = 12.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                if (currentQty > 0) {
                    Text(
                        text = "Calculated Total: ${CurrencyFormatter.formatInr(totalAmount)}",
                        fontWeight = FontWeight.Bold,
                        color = BrandEmerald,
                        fontSize = 16.sp
                    )
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (currentQty > 0) onConfirm(currentQty)
                },
                enabled = currentQty > 0,
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Add to Order")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel")
            }
        }
    )
}
