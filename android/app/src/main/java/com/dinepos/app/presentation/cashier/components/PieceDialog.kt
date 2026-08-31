package com.dinepos.app.presentation.cashier.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Remove
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.ItemVariant
import com.dinepos.app.domain.model.MenuItem

@Composable
fun PieceDialog(
    item: MenuItem,
    variant: ItemVariant,
    onDismiss: () -> Unit,
    onConfirm: (quantity: Double) -> Unit
) {
    var quantity by remember { mutableStateOf(1) }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text(text = "🥚 ", fontSize = 22.sp)
                Text(text = item.name, fontWeight = FontWeight.Bold)
            }
        },
        text = {
            Column(
                modifier = Modifier.fillMaxWidth(),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Text(
                    text = "${CurrencyFormatter.formatInr(variant.price)} / piece",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.Bold,
                    color = BrandDark
                )
                Text(
                    text = "Select piece quantity:",
                    style = MaterialTheme.typography.bodyMedium,
                    color = TextSecondary
                )

                Spacer(modifier = Modifier.height(20.dp))

                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.Center,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    OutlinedIconButton(
                        onClick = { if (quantity > 1) quantity-- },
                        shape = CircleShape,
                        border = BorderStroke(1.5.dp, BrandBorder),
                        modifier = Modifier.size(48.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Remove,
                            contentDescription = "Decrease",
                            tint = BrandDark,
                            modifier = Modifier.size(24.dp)
                        )
                    }

                    Text(
                        text = "$quantity",
                        fontSize = 28.sp,
                        fontWeight = FontWeight.ExtraBold,
                        color = BrandDark,
                        modifier = Modifier.padding(horizontal = 24.dp)
                    )

                    FilledIconButton(
                        onClick = { quantity++ },
                        shape = CircleShape,
                        colors = IconButtonDefaults.filledIconButtonColors(containerColor = BrandOrange),
                        modifier = Modifier.size(48.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Add,
                            contentDescription = "Increase",
                            tint = Color.White,
                            modifier = Modifier.size(24.dp)
                        )
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                Text(
                    text = "Total: ${CurrencyFormatter.formatInr(variant.price * quantity)}",
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp,
                    color = BrandOrange
                )
            }
        },
        confirmButton = {
            Button(
                onClick = { onConfirm(quantity.toDouble()) },
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
