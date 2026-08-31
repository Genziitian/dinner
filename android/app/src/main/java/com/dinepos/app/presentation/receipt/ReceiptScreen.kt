package com.dinepos.app.presentation.receipt

import android.content.Intent
import android.graphics.Bitmap
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Share
import androidx.compose.material3.*
import androidx.compose.runtime.*
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
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.CurrencyFormatter
import com.dinepos.app.core.utils.ReceiptPrintHelper
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.Order
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReceiptScreen(
    orderId: Int = 0,
    token: String? = null,
    onNavigateBack: () -> Unit
) {
    val context = LocalContext.current
    val sessionManager = DinePosApp.instance.sessionManager
    val orderRepository = DinePosApp.instance.orderRepository
    val scope = rememberCoroutineScope()

    var order by remember { mutableStateOf<Order?>(null) }
    var qrBitmap by remember { mutableStateOf<Bitmap?>(null) }
    var isLoading by remember { mutableStateOf(true) }
    var errorMessage by remember { mutableStateOf<String?>(null) }
    val restaurantName = remember { sessionManager.getRestaurantName().ifBlank { "DinePOS Restaurant" } }

    LaunchedEffect(orderId, token) {
        scope.launch {
            isLoading = true
            errorMessage = null
            val result = when {
                orderId > 0 -> orderRepository.getOrderById(orderId)
                !token.isNullOrBlank() -> orderRepository.getReceiptByToken(token)
                else -> Resource.Error("No order ID or receipt token provided.")
            }

            when (result) {
                is Resource.Success -> {
                    val ord = result.data
                    order = ord
                    val tokenToUse = token ?: ord.receiptToken ?: ""
                    val publicUrl = if (tokenToUse.isNotBlank()) "${sessionManager.getBaseUrl()}receipt/$tokenToUse" else ""

                    if (publicUrl.isNotBlank()) {
                        qrBitmap = withContext(Dispatchers.Default) {
                            QrBitmapGenerator.generateQrBitmap(publicUrl, 400)
                        }
                    }
                    isLoading = false
                }
                is Resource.Error -> {
                    errorMessage = result.message
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
            TopAppBar(
                title = {
                    Text(
                        text = if (order != null) "Receipt #${order?.orderNumber}" else "Order Receipt",
                        fontWeight = FontWeight.Bold
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    if (order != null) {
                        IconButton(onClick = {
                            val currentOrder = order ?: return@IconButton
                            val shareText = buildString {
                                appendLine("🧾 $restaurantName")
                                appendLine("Order #${currentOrder.orderNumber} · ${currentOrder.orderDate} ${currentOrder.orderTime}")
                                appendLine("-----------------------------")
                                currentOrder.items.forEach { item ->
                                    appendLine("${item.itemName} (${item.variantName}) x ${CurrencyFormatter.formatQuantity(item.quantity, item.unit)} = ${CurrencyFormatter.formatInr(item.totalPrice)}")
                                }
                                appendLine("-----------------------------")
                                appendLine("Total: ${CurrencyFormatter.formatInr(currentOrder.total)} (${currentOrder.paymentMethod})")
                            }
                            val intent = Intent(Intent.ACTION_SEND).apply {
                                type = "text/plain"
                                putExtra(Intent.EXTRA_SUBJECT, "Receipt #${currentOrder.orderNumber}")
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
            if (isLoading) {
                CircularProgressIndicator(color = BrandOrange, modifier = Modifier.align(Alignment.Center))
            } else if (order == null) {
                Text(
                    text = errorMessage ?: "Receipt not found.",
                    color = TextSecondary,
                    modifier = Modifier.align(Alignment.Center)
                )
            } else {
                val currentOrder = order!!
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .widthIn(max = 500.dp)
                        .verticalScroll(rememberScrollState()),
                    shape = RoundedCornerShape(16.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White),
                    border = BorderStroke(1.dp, BrandBorder),
                    elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(20.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        // Success Icon & Header
                        Icon(
                            imageVector = Icons.Default.CheckCircle,
                            contentDescription = "Success",
                            tint = BrandEmerald,
                            modifier = Modifier.size(48.dp)
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Order Placed Successfully",
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.Bold,
                            color = BrandEmerald
                        )

                        Spacer(modifier = Modifier.height(16.dp))

                        // Restaurant Info
                        Text(
                            text = restaurantName,
                            style = MaterialTheme.typography.headlineSmall,
                            fontWeight = FontWeight.Black,
                            color = BrandDark
                        )
                        Text(
                            text = "Order #${currentOrder.orderNumber}",
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.ExtraBold,
                            color = BrandOrange
                        )
                        Text(
                            text = "${currentOrder.orderDate}  •  ${currentOrder.orderTime}",
                            style = MaterialTheme.typography.bodySmall,
                            color = TextSecondary
                        )

                        if (!currentOrder.customerName.isNullOrBlank()) {
                            Text(
                                text = "Customer: ${currentOrder.customerName}",
                                style = MaterialTheme.typography.bodyMedium,
                                fontWeight = FontWeight.SemiBold,
                                color = BrandDark
                            )
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                        HorizontalDivider(color = BrandBorder, thickness = 1.dp)
                        Spacer(modifier = Modifier.height(12.dp))

                        // Items Breakdown
                        Column(
                            modifier = Modifier.fillMaxWidth(),
                            verticalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            currentOrder.items.forEach { item ->
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Column(modifier = Modifier.weight(1f)) {
                                        Text(
                                            text = item.itemName,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 14.sp,
                                            color = BrandDark
                                        )
                                        Text(
                                            text = "${item.variantName} × ${CurrencyFormatter.formatQuantity(item.quantity, item.unit)} @ ${CurrencyFormatter.formatInr(item.unitPrice)}",
                                            style = MaterialTheme.typography.bodySmall,
                                            color = TextSecondary,
                                            fontSize = 12.sp
                                        )
                                    }
                                    Text(
                                        text = CurrencyFormatter.formatInr(item.totalPrice),
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 14.sp,
                                        fontFamily = FontFamily.Monospace,
                                        color = BrandDark
                                    )
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(14.dp))
                        HorizontalDivider(color = BrandBorder, thickness = 1.dp)
                        Spacer(modifier = Modifier.height(12.dp))

                        // Totals Summary
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(text = "Subtotal", color = TextSecondary)
                            Text(text = CurrencyFormatter.formatInr(currentOrder.subtotal), fontWeight = FontWeight.Bold)
                        }

                        Spacer(modifier = Modifier.height(6.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(text = "Payment Method", color = TextSecondary)
                            Text(text = currentOrder.paymentMethod, fontWeight = FontWeight.SemiBold)
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
                                text = CurrencyFormatter.formatInr(currentOrder.total),
                                style = MaterialTheme.typography.headlineMedium,
                                fontWeight = FontWeight.Black,
                                color = BrandDark
                            )
                        }

                        Spacer(modifier = Modifier.height(20.dp))

                        // QR Code View
                        val qr = qrBitmap
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
                                        currentOrder,
                                        restaurantName
                                    )
                                },
                                shape = RoundedCornerShape(12.dp),
                                border = BorderStroke(1.5.dp, BrandDark),
                                modifier = Modifier
                                    .weight(1f)
                                    .height(50.dp)
                            ) {
                                Text("📄 Print / PDF", fontWeight = FontWeight.Bold, color = BrandDark)
                            }

                            Button(
                                onClick = onNavigateBack,
                                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                                shape = RoundedCornerShape(12.dp),
                                modifier = Modifier
                                    .weight(1f)
                                    .height(50.dp)
                            ) {
                                Text("✨ Next Order", fontWeight = FontWeight.Bold, color = Color.White)
                            }
                        }
                    }
                }
            }
        }
    }
}
