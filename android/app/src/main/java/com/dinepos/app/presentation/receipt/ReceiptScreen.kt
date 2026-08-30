package com.dinepos.app.presentation.receipt

import android.content.Intent
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Share
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.ReceiptPrintHelper

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReceiptScreen(
    onNavigateBack: () -> Unit,
    viewModel: ReceiptViewModel = viewModel()
) {
    val uiState by viewModel.uiState.collectAsState()
    val context = LocalContext.current
    val order = uiState.order

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = if (order != null) "Receipt #${order.orderNumber}" else "Order Receipt",
                        fontWeight = FontWeight.Bold
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    if (order != null) {
                        IconButton(onClick = {
                            val shareText = buildString {
                                appendLine("🧾 ${uiState.restaurantName}")
                                appendLine("Order #${order.orderNumber} · ${order.orderDate} ${order.orderTime}")
                                appendLine("-----------------------------")
                                order.items.forEach { item ->
                                    appendLine("${item.itemName} (${item.variantName}) x ${CurrencyFormatter.formatQuantity(item.quantity, item.unit)} = ${CurrencyFormatter.formatInr(item.totalPrice)}")
                                }
                                appendLine("-----------------------------")
                                appendLine("Total: ${CurrencyFormatter.formatInr(order.total)} (${order.paymentMethod})")
                            }
                            val intent = Intent(Intent.ACTION_SEND).apply {
                                type = "text/plain"
                                putExtra(Intent.EXTRA_SUBJECT, "Receipt #${order.orderNumber}")
                                putExtra(Intent.EXTRA_TEXT, shareText)
                            }
                            context.startActivity(Intent.createChooser(intent, "Share Receipt"))
                        }) {
                            Icon(Icons.Default.Share, contentDescription = "Share Receipt")
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
            )
        }
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp),
            contentAlignment = Alignment.TopCenter
        ) {
            if (uiState.isLoading) {
                CircularProgressIndicator(color = BrandOrange, modifier = Modifier.align(Alignment.Center))
            } else if (order == null) {
                Text(
                    text = uiState.errorMessage ?: "Receipt not found.",
                    color = TextSecondary,
                    modifier = Modifier.align(Alignment.Center)
                )
            } else {
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .verticalScroll(rememberScrollState()),
                    shape = RoundedCornerShape(20.dp),
                    colors = CardDefaults.cardColors(containerColor = BrandSurface),
                    border = BorderStroke(1.dp, BrandBorder),
                    elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        // Success Badge
                        Icon(
                            imageVector = Icons.Default.CheckCircle,
                            contentDescription = null,
                            tint = BrandEmerald,
                            modifier = Modifier.size(48.dp)
                        )

                        Spacer(modifier = Modifier.height(8.dp))

                        Text(
                            text = uiState.restaurantName.ifBlank { "DinePOS Restaurant" },
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.ExtraBold,
                            color = BrandDark
                        )

                        Text(
                            text = "ORDER #${order.orderNumber}",
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Black,
                            color = BrandOrangeDark
                        )

                        Text(
                            text = "${order.orderDate} · ${order.orderTime}",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextSecondary
                        )

                        if (!order.customerName.isNullOrBlank()) {
                            Text(
                                text = "Customer: ${order.customerName}",
                                style = MaterialTheme.typography.bodyMedium,
                                fontWeight = FontWeight.SemiBold,
                                color = BrandDark
                            )
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                        Divider(color = BrandBorder, thickness = 1.dp)
                        Spacer(modifier = Modifier.height(16.dp))

                        // Items Breakdown
                        Column(
                            modifier = Modifier.fillMaxWidth(),
                            verticalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            order.items.forEach { item ->
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Column(modifier = Modifier.weight(1f)) {
                                        Text(
                                            text = item.itemName,
                                            fontWeight = FontWeight.Bold,
                                            color = BrandDark,
                                            fontSize = 15.sp
                                        )
                                        Text(
                                            text = "${item.variantName} × ${CurrencyFormatter.formatQuantity(item.quantity, item.unit)}",
                                            style = MaterialTheme.typography.bodySmall,
                                            color = TextSecondary
                                        )
                                    }
                                    Text(
                                        text = CurrencyFormatter.formatInr(item.totalPrice),
                                        fontWeight = FontWeight.Bold,
                                        color = BrandDark,
                                        fontSize = 15.sp
                                    )
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                        Divider(color = BrandBorder, thickness = 1.dp)
                        Spacer(modifier = Modifier.height(16.dp))

                        // Totals Summary
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(text = "Subtotal", color = TextSecondary)
                            Text(text = CurrencyFormatter.formatInr(order.subtotal), fontWeight = FontWeight.Bold)
                        }

                        Spacer(modifier = Modifier.height(6.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(text = "Payment Method", color = TextSecondary)
                            Text(text = order.paymentMethod, fontWeight = FontWeight.SemiBold)
                        }

                        Spacer(modifier = Modifier.height(10.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "TOTAL PAID",
                                style = MaterialTheme.typography.titleMedium,
                                fontWeight = FontWeight.ExtraBold,
                                color = BrandDark
                            )
                            Text(
                                text = CurrencyFormatter.formatInr(order.total),
                                style = MaterialTheme.typography.headlineMedium,
                                fontWeight = FontWeight.Black,
                                color = BrandDark
                            )
                        }

                        Spacer(modifier = Modifier.height(20.dp))

                        // QR Code View
                        val qr = uiState.qrBitmap
                        if (qr != null) {
                            Surface(
                                shape = RoundedCornerShape(12.dp),
                                border = BorderStroke(1.dp, BrandBorder),
                                color = Color.White,
                                modifier = Modifier.padding(8.dp)
                            ) {
                                Image(
                                    bitmap = qr.asImageBitmap(),
                                    contentDescription = "Receipt QR Code",
                                    modifier = Modifier.size(160.dp).padding(8.dp)
                                )
                            }
                            Text(
                                text = "Scan for cryptographic digital receipt",
                                fontSize = 11.sp,
                                color = TextMuted,
                                textAlign = TextAlign.Center
                            )
                        }

                        Spacer(modifier = Modifier.height(20.dp))

                        // Action Buttons: Print / PDF & Next Order
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            OutlinedButton(
                                onClick = {
                                    ReceiptPrintHelper.printOrDownloadPdf(
                                        context,
                                        order,
                                        uiState.restaurantName.ifBlank { "DinePOS Restaurant" }
                                    )
                                },
                                shape = RoundedCornerShape(12.dp),
                                border = BorderStroke(1.5.dp, BrandOrange),
                                modifier = Modifier
                                    .weight(1f)
                                    .height(50.dp)
                            ) {
                                Text("📄 Download PDF", fontWeight = FontWeight.Bold, color = BrandOrange)
                            }

                            Button(
                                onClick = onNavigateBack,
                                colors = ButtonDefaults.buttonColors(containerColor = BrandDark),
                                shape = RoundedCornerShape(12.dp),
                                modifier = Modifier
                                    .weight(1f)
                                    .height(50.dp)
                            ) {
                                Text("Done", fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }
        }
    }
}
