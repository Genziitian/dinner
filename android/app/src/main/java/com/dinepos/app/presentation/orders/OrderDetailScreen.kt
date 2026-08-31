package com.dinepos.app.presentation.orders

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.automirrored.outlined.ReceiptLong
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.Order
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun OrderDetailScreen(
    orderId: Int,
    onNavigateBack: () -> Unit,
    onViewReceipt: (Int) -> Unit
) {
    var order by remember { mutableStateOf<Order?>(null) }
    var isLoading by remember { mutableStateOf(true) }
    val scope = rememberCoroutineScope()

    LaunchedEffect(orderId) {
        scope.launch {
            isLoading = true
            when (val result = DinePosApp.instance.orderRepository.getOrderById(orderId)) {
                is Resource.Success -> {
                    order = result.data
                    isLoading = false
                }
                else -> {
                    isLoading = false
                }
            }
        }
    }

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            Column {
                TopAppBar(
                    title = {
                        Text(
                            text = if (order != null) "Order #${order?.orderNumber}" else "Order Details",
                            fontWeight = FontWeight.ExtraBold,
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
                        if (order != null) {
                            IconButton(onClick = { onViewReceipt(orderId) }) {
                                Icon(
                                    imageVector = Icons.AutoMirrored.Outlined.ReceiptLong,
                                    contentDescription = "View Receipt",
                                    tint = BrandOrange
                                )
                            }
                        }
                    },
                    colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White)
                )
                HorizontalDivider(color = BrandBorder.copy(alpha = 0.7f), thickness = 1.dp)
            }
        }
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp)
        ) {
            if (isLoading) {
                CircularProgressIndicator(color = BrandOrange, modifier = Modifier.align(Alignment.Center))
            } else if (order != null) {
                val ord = order!!
                Card(
                    shape = RoundedCornerShape(20.dp),
                    colors = CardDefaults.cardColors(containerColor = BrandSurface),
                    border = BorderStroke(1.dp, BrandBorder),
                    modifier = Modifier
                        .fillMaxWidth()
                        .verticalScroll(rememberScrollState())
                ) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "Order #${ord.orderNumber}",
                                style = MaterialTheme.typography.titleLarge,
                                fontWeight = FontWeight.ExtraBold,
                                color = BrandDark
                            )
                            Surface(
                                shape = RoundedCornerShape(6.dp),
                                color = BrandEmeraldLight
                            ) {
                                Text(
                                    text = ord.status.uppercase(),
                                    color = BrandEmerald,
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 11.sp,
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(8.dp))
                        Text(text = "Date & Time: ${ord.orderDate} at ${ord.orderTime}", color = TextSecondary, fontSize = 13.sp)
                        if (!ord.customerName.isNullOrBlank()) {
                            Text(text = "Customer: ${ord.customerName} (${ord.customerPhone ?: "No phone"})", color = TextSecondary, fontSize = 13.sp)
                        }
                        if (!ord.createdByUsername.isNullOrBlank()) {
                            Text(text = "Billed by: ${ord.createdByUsername}", color = TextSecondary, fontSize = 13.sp)
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                        HorizontalDivider(color = BrandBorder)
                        Spacer(modifier = Modifier.height(16.dp))

                        Text(text = "ORDER ITEMS", fontWeight = FontWeight.Bold, fontSize = 13.sp, color = TextSecondary)
                        Spacer(modifier = Modifier.height(10.dp))

                        ord.items.forEach { item ->
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 4.dp),
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(text = item.itemName, fontWeight = FontWeight.Bold, color = BrandDark)
                                    Text(
                                        text = "${item.variantName} × ${CurrencyFormatter.formatQuantity(item.quantity, item.unit)} @ ${CurrencyFormatter.formatInr(item.unitPrice)}",
                                        fontSize = 12.sp,
                                        color = TextSecondary
                                    )
                                }
                                Text(
                                    text = CurrencyFormatter.formatInr(item.totalPrice),
                                    fontWeight = FontWeight.Bold,
                                    color = BrandDark
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                        HorizontalDivider(color = BrandBorder)
                        Spacer(modifier = Modifier.height(16.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(text = "TOTAL (${ord.paymentMethod})", fontWeight = FontWeight.Bold, color = BrandDark)
                            Text(
                                text = CurrencyFormatter.formatInr(ord.total),
                                style = MaterialTheme.typography.titleLarge,
                                fontWeight = FontWeight.Black,
                                color = BrandOrangeDark
                            )
                        }

                        Spacer(modifier = Modifier.height(24.dp))

                        // View Order Receipt Action Button
                        Button(
                            onClick = { onViewReceipt(ord.id) },
                            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                            shape = RoundedCornerShape(14.dp),
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(50.dp)
                        ) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Outlined.ReceiptLong,
                                contentDescription = "Receipt",
                                tint = Color.White,
                                modifier = Modifier.size(20.dp)
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Text(
                                text = "View Order Receipt",
                                fontWeight = FontWeight.Bold,
                                fontSize = 15.sp,
                                color = Color.White
                            )
                        }
                    }
                }
            }
        }
    }
}
