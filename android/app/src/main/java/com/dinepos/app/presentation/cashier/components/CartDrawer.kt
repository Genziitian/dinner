package com.dinepos.app.presentation.cashier.components

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.itemsIndexed
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.DeleteOutline
import androidx.compose.material.icons.filled.Remove
import androidx.compose.material.icons.filled.ShoppingBag
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.domain.model.CartItem

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CartBottomSheet(
    cartItems: List<CartItem>,
    customerName: String,
    customerPhone: String,
    selectedPayment: String,
    previewOrderNumber: Int,
    isSubmitting: Boolean,
    onCustomerNameChange: (String) -> Unit,
    onCustomerPhoneChange: (String) -> Unit,
    onPaymentMethodChange: (String) -> Unit,
    onUpdateQuantity: (index: Int, delta: Double) -> Unit,
    onRemoveItem: (index: Int) -> Unit,
    onClearCart: () -> Unit,
    onSubmitOrder: () -> Unit,
    onDismiss: () -> Unit
) {
    ModalBottomSheet(
        onDismissRequest = onDismiss,
        containerColor = BrandSurface,
        shape = RoundedCornerShape(topStart = 24.dp, topEnd = 24.dp),
        dragHandle = { BottomSheetDefaults.DragHandle() }
    ) {
        CartContent(
            cartItems = cartItems,
            customerName = customerName,
            customerPhone = customerPhone,
            selectedPayment = selectedPayment,
            previewOrderNumber = previewOrderNumber,
            isSubmitting = isSubmitting,
            onCustomerNameChange = onCustomerNameChange,
            onCustomerPhoneChange = onCustomerPhoneChange,
            onPaymentMethodChange = onPaymentMethodChange,
            onUpdateQuantity = onUpdateQuantity,
            onRemoveItem = onRemoveItem,
            onClearCart = onClearCart,
            onSubmitOrder = onSubmitOrder,
            isTabletPanel = false,
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 20.dp)
                .padding(bottom = 24.dp)
        )
    }
}

@Composable
fun CartPanel(
    cartItems: List<CartItem>,
    customerName: String,
    customerPhone: String,
    selectedPayment: String,
    previewOrderNumber: Int,
    isSubmitting: Boolean,
    onCustomerNameChange: (String) -> Unit,
    onCustomerPhoneChange: (String) -> Unit,
    onPaymentMethodChange: (String) -> Unit,
    onUpdateQuantity: (index: Int, delta: Double) -> Unit,
    onRemoveItem: (index: Int) -> Unit,
    onClearCart: () -> Unit,
    onSubmitOrder: () -> Unit,
    modifier: Modifier = Modifier
) {
    Surface(
        shape = RoundedCornerShape(16.dp),
        color = BrandSurface,
        border = BorderStroke(1.dp, BrandBorder),
        shadowElevation = 4.dp,
        modifier = modifier
    ) {
        CartContent(
            cartItems = cartItems,
            customerName = customerName,
            customerPhone = customerPhone,
            selectedPayment = selectedPayment,
            previewOrderNumber = previewOrderNumber,
            isSubmitting = isSubmitting,
            onCustomerNameChange = onCustomerNameChange,
            onCustomerPhoneChange = onCustomerPhoneChange,
            onPaymentMethodChange = onPaymentMethodChange,
            onUpdateQuantity = onUpdateQuantity,
            onRemoveItem = onRemoveItem,
            onClearCart = onClearCart,
            onSubmitOrder = onSubmitOrder,
            isTabletPanel = true,
            modifier = Modifier
                .fillMaxSize()
                .padding(16.dp)
        )
    }
}

@Composable
private fun CartContent(
    cartItems: List<CartItem>,
    customerName: String,
    customerPhone: String,
    selectedPayment: String,
    previewOrderNumber: Int,
    isSubmitting: Boolean,
    onCustomerNameChange: (String) -> Unit,
    onCustomerPhoneChange: (String) -> Unit,
    onPaymentMethodChange: (String) -> Unit,
    onUpdateQuantity: (index: Int, delta: Double) -> Unit,
    onRemoveItem: (index: Int) -> Unit,
    onClearCart: () -> Unit,
    onSubmitOrder: () -> Unit,
    isTabletPanel: Boolean,
    modifier: Modifier = Modifier
) {
    val subtotal = cartItems.sumOf { it.lineTotal }

    Column(modifier = modifier) {
        // Header: Title + Preview Order # + Clear Button
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column {
                Text(
                    text = "Current Order",
                    style = MaterialTheme.typography.titleLarge,
                    fontWeight = FontWeight.ExtraBold,
                    color = BrandDark
                )
                Text(
                    text = "Order #$previewOrderNumber",
                    style = MaterialTheme.typography.bodySmall,
                    color = TextSecondary
                )
            }

            if (cartItems.isNotEmpty()) {
                TextButton(onClick = onClearCart) {
                    Text(text = "Clear Cart", color = StatusError, fontWeight = FontWeight.Bold)
                }
            }
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Optional Customer Inputs
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            OutlinedTextField(
                value = customerName,
                onValueChange = onCustomerNameChange,
                label = { Text("Customer (Opt)", fontSize = 12.sp) },
                singleLine = true,
                shape = RoundedCornerShape(10.dp),
                modifier = Modifier.weight(1f)
            )
            OutlinedTextField(
                value = customerPhone,
                onValueChange = onCustomerPhoneChange,
                label = { Text("Phone (Opt)", fontSize = 12.sp) },
                singleLine = true,
                shape = RoundedCornerShape(10.dp),
                modifier = Modifier.weight(1f)
            )
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Cart Items List
        if (cartItems.isEmpty()) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .then(if (isTabletPanel) Modifier.weight(1f) else Modifier.height(140.dp)),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Icon(
                        imageVector = Icons.Default.ShoppingBag,
                        contentDescription = null,
                        tint = TextMuted,
                        modifier = Modifier.size(36.dp)
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(text = "Cart is empty", color = TextSecondary, fontWeight = FontWeight.SemiBold)
                    Text(text = "Tap menu items to add to this order", color = TextMuted, fontSize = 11.sp)
                }
            }
        } else {
            LazyColumn(
                modifier = Modifier
                    .fillMaxWidth()
                    .then(if (isTabletPanel) Modifier.weight(1f) else Modifier.weight(1f, fill = false).heightIn(max = 240.dp)),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                itemsIndexed(cartItems) { index, cartItem ->
                    Surface(
                        shape = RoundedCornerShape(12.dp),
                        color = BrandBackground,
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(8.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column(modifier = Modifier.weight(1f)) {
                                Text(
                                    text = cartItem.item.name,
                                    fontWeight = FontWeight.Bold,
                                    color = BrandDark,
                                    maxLines = 1,
                                    overflow = TextOverflow.Ellipsis,
                                    fontSize = 13.sp
                                )
                                Text(
                                    text = "${cartItem.variantName} · ${CurrencyFormatter.formatInr(cartItem.unitPrice)}",
                                    style = MaterialTheme.typography.bodySmall,
                                    color = TextSecondary,
                                    fontSize = 11.sp
                                )
                            }

                            Row(
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.spacedBy(4.dp)
                            ) {
                                OutlinedIconButton(
                                    onClick = { onUpdateQuantity(index, -1.0) },
                                    modifier = Modifier.size(28.dp),
                                    shape = CircleShape
                                ) {
                                    Icon(Icons.Default.Remove, contentDescription = "Decrease", modifier = Modifier.size(14.dp))
                                }

                                Text(
                                    text = CurrencyFormatter.formatQuantity(cartItem.quantity, cartItem.unit),
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 12.sp,
                                    color = BrandDark
                                )

                                OutlinedIconButton(
                                    onClick = { onUpdateQuantity(index, 1.0) },
                                    modifier = Modifier.size(28.dp),
                                    shape = CircleShape
                                ) {
                                    Icon(Icons.Default.Add, contentDescription = "Increase", modifier = Modifier.size(14.dp))
                                }

                                Spacer(modifier = Modifier.width(4.dp))

                                Text(
                                    text = CurrencyFormatter.formatInr(cartItem.lineTotal),
                                    fontWeight = FontWeight.ExtraBold,
                                    fontSize = 13.sp,
                                    color = BrandDark
                                )

                                IconButton(
                                    onClick = { onRemoveItem(index) },
                                    modifier = Modifier.size(26.dp)
                                ) {
                                    Icon(
                                        imageVector = Icons.Default.DeleteOutline,
                                        contentDescription = "Remove",
                                        tint = StatusError,
                                        modifier = Modifier.size(16.dp)
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Total Amount Row
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = "TOTAL AMOUNT:",
                style = MaterialTheme.typography.titleSmall,
                fontWeight = FontWeight.Bold,
                color = TextSecondary
            )
            Text(
                text = CurrencyFormatter.formatInr(subtotal),
                style = MaterialTheme.typography.headlineSmall,
                fontWeight = FontWeight.ExtraBold,
                color = BrandDark
            )
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Payment Method Selector
        Text(
            text = "PAYMENT METHOD",
            style = MaterialTheme.typography.labelSmall,
            fontWeight = FontWeight.Bold,
            color = TextSecondary
        )
        Spacer(modifier = Modifier.height(4.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            listOf("Cash" to "💵 Cash", "Online / UPI" to "📱 Online / UPI").forEach { (method, label) ->
                val isSelected = selectedPayment == method
                Surface(
                    shape = RoundedCornerShape(10.dp),
                    color = if (isSelected) {
                        if (method == "Cash") BrandEmeraldLight else BrandOrangeLight
                    } else BrandSurface,
                    border = BorderStroke(
                        if (isSelected) 2.dp else 1.dp,
                        if (isSelected) {
                            if (method == "Cash") BrandEmerald else BrandOrange
                        } else BrandBorder
                    ),
                    modifier = Modifier
                        .weight(1f)
                        .clickable { onPaymentMethodChange(method) }
                ) {
                    Box(
                        modifier = Modifier.padding(vertical = 10.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = label,
                            fontWeight = FontWeight.Bold,
                            fontSize = 13.sp,
                            color = if (isSelected) BrandDark else TextSecondary
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(12.dp))

        // Checkout Button
        Button(
            onClick = onSubmitOrder,
            enabled = cartItems.isNotEmpty() && !isSubmitting,
            shape = RoundedCornerShape(12.dp),
            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
            modifier = Modifier
                .fillMaxWidth()
                .height(48.dp)
        ) {
            if (isSubmitting) {
                CircularProgressIndicator(color = Color.White, modifier = Modifier.size(22.dp), strokeWidth = 2.5.dp)
            } else {
                Text(
                    text = "Save & Bill Order (${CurrencyFormatter.formatInr(subtotal)})",
                    style = MaterialTheme.typography.titleSmall,
                    fontWeight = FontWeight.Bold,
                    color = Color.White
                )
            }
        }
    }
}
