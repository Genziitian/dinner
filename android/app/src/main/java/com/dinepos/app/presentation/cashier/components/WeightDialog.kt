package com.dinepos.app.presentation.cashier.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.ItemVariant
import com.dinepos.app.domain.model.MenuItem
import java.util.Locale

@Composable
fun WeightDialog(
    item: MenuItem,
    variant: ItemVariant,
    onDismiss: () -> Unit,
    onConfirm: (quantity: Double) -> Unit
) {
    var mode by remember { mutableStateOf(0) } // 0 = By Weight, 1 = By Rupees (₹)
    var weightText by remember { mutableStateOf("1.0") }
    var rupeeText by remember { mutableStateOf("50") }

    val unit = variant.quantityUnit.ifBlank { item.baseUnit }
    val pricePerUnit = variant.price

    val finalQty = if (mode == 0) {
        weightText.toDoubleOrNull() ?: 0.0
    } else {
        val rupees = rupeeText.toDoubleOrNull() ?: 0.0
        if (pricePerUnit > 0) rupees / pricePerUnit else 0.0
    }

    val finalTotal = finalQty * pricePerUnit

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color.White,
        titleContentColor = BrandDark,
        textContentColor = TextPrimary,
        shape = RoundedCornerShape(20.dp),
        title = {
            Text(
                text = item.name,
                fontWeight = FontWeight.ExtraBold,
                fontSize = 18.sp,
                color = BrandDark
            )
        },
        text = {
            Column(modifier = Modifier.fillMaxWidth()) {
                // Rate Banner
                Surface(
                    shape = RoundedCornerShape(12.dp),
                    color = Color(0xFFF1F5F9),
                    border = BorderStroke(1.dp, BrandBorder),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(
                        modifier = Modifier.padding(12.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(text = "Unit Rate:", color = TextSecondary, fontWeight = FontWeight.SemiBold, fontSize = 13.sp)
                        Text(
                            text = "${CurrencyFormatter.formatInr(pricePerUnit)} / $unit",
                            fontWeight = FontWeight.ExtraBold,
                            color = BrandDark,
                            fontSize = 15.sp
                        )
                    }
                }

                Spacer(modifier = Modifier.height(14.dp))

                // Mode Selector Segmented Pill (By Weight vs By Rupees)
                Surface(
                    shape = RoundedCornerShape(12.dp),
                    color = Color(0xFFF1F5F9),
                    border = BorderStroke(1.dp, BrandBorder),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(modifier = Modifier.padding(4.dp)) {
                        Box(
                            modifier = Modifier
                                .weight(1f)
                                .background(
                                    if (mode == 0) BrandDark else Color.Transparent,
                                    RoundedCornerShape(8.dp)
                                )
                                .clickable { mode = 0 }
                                .padding(vertical = 8.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                text = "By Weight ($unit)",
                                fontSize = 13.sp,
                                fontWeight = FontWeight.Bold,
                                color = if (mode == 0) Color.White else TextSecondary
                            )
                        }
                        Box(
                            modifier = Modifier
                                .weight(1f)
                                .background(
                                    if (mode == 1) BrandDark else Color.Transparent,
                                    RoundedCornerShape(8.dp)
                                )
                                .clickable { mode = 1 }
                                .padding(vertical = 8.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                text = "By Rupees (₹ Amount)",
                                fontSize = 13.sp,
                                fontWeight = FontWeight.Bold,
                                color = if (mode == 1) Color.White else TextSecondary
                            )
                        }
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                if (mode == 0) {
                    // BY WEIGHT INPUT
                    OutlinedTextField(
                        value = weightText,
                        onValueChange = { weightText = it },
                        label = { Text("Quantity in ${unit.uppercase()}", color = TextSecondary) },
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                        singleLine = true,
                        shape = RoundedCornerShape(12.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedTextColor = BrandDark,
                            unfocusedTextColor = BrandDark,
                            focusedBorderColor = BrandOrange,
                            unfocusedBorderColor = BrandBorder
                        ),
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(10.dp))

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(6.dp)
                    ) {
                        listOf(0.25, 0.5, 1.0, 2.0).forEach { preset ->
                            OutlinedButton(
                                onClick = { weightText = preset.toString() },
                                shape = RoundedCornerShape(8.dp),
                                border = BorderStroke(1.dp, BrandBorder),
                                contentPadding = PaddingValues(horizontal = 4.dp, vertical = 4.dp),
                                modifier = Modifier.weight(1f)
                            ) {
                                Text(text = "$preset", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                            }
                        }
                    }
                } else {
                    // BY RUPEES INPUT (Auto-Calculator)
                    OutlinedTextField(
                        value = rupeeText,
                        onValueChange = { rupeeText = it },
                        label = { Text("Enter Amount in Rupees (₹)", color = TextSecondary) },
                        placeholder = { Text("e.g. 10, 12, 50, 100", color = Color.Gray) },
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        singleLine = true,
                        shape = RoundedCornerShape(12.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedTextColor = BrandDark,
                            unfocusedTextColor = BrandDark,
                            focusedBorderColor = BrandOrange,
                            unfocusedBorderColor = BrandBorder
                        ),
                        modifier = Modifier.fillMaxWidth()
                    )

                    Spacer(modifier = Modifier.height(10.dp))

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(6.dp)
                    ) {
                        listOf(10, 20, 50, 100).forEach { preset ->
                            OutlinedButton(
                                onClick = { rupeeText = preset.toString() },
                                shape = RoundedCornerShape(8.dp),
                                border = BorderStroke(1.dp, BrandBorder),
                                contentPadding = PaddingValues(horizontal = 4.dp, vertical = 4.dp),
                                modifier = Modifier.weight(1f)
                            ) {
                                Text(text = "₹$preset", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = BrandDark)
                            }
                        }
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                // Calculated Output Result
                if (finalQty > 0) {
                    Surface(
                        shape = RoundedCornerShape(10.dp),
                        color = Color(0xFFF0FDF4),
                        border = BorderStroke(1.dp, Color(0xFFBBF7D0)),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(10.dp)) {
                            val formattedWeight = String.format(Locale.US, "%.3f", finalQty)
                            Text(
                                text = "Gives: $formattedWeight $unit",
                                fontWeight = FontWeight.ExtraBold,
                                color = Color(0xFF15803D),
                                fontSize = 14.sp
                            )
                            Text(
                                text = "Total Bill: ${CurrencyFormatter.formatInr(finalTotal)}",
                                fontWeight = FontWeight.Bold,
                                color = BrandDark,
                                fontSize = 13.sp
                            )
                        }
                    }
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (finalQty > 0) onConfirm(finalQty)
                },
                enabled = finalQty > 0,
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Add to Order", fontWeight = FontWeight.Bold, color = Color.White)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel", fontWeight = FontWeight.SemiBold, color = TextSecondary)
            }
        }
    )
}
