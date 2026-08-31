package com.dinepos.app.presentation.scanner

import android.Manifest
import android.content.pm.PackageManager
import android.widget.Toast
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.animation.core.*
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CameraAlt
import androidx.compose.material.icons.filled.QrCodeScanner
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.core.content.ContextCompat
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*
import com.dinepos.app.core.utils.Resource
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun QrScannerScreen(
    onNavigateBack: () -> Unit,
    onReceiptFound: (orderId: Int, token: String?) -> Unit
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    var manualInput by remember { mutableStateOf("") }
    var isResolving by remember { mutableStateOf(false) }
    var errorMessage by remember { mutableStateOf<String?>(null) }

    // Camera Permission State
    var hasCameraPermission by remember {
        mutableStateOf(
            ContextCompat.checkSelfPermission(
                context,
                Manifest.permission.CAMERA
            ) == PackageManager.PERMISSION_GRANTED
        )
    }

    val permissionLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        hasCameraPermission = isGranted
        if (!isGranted) {
            Toast.makeText(
                context,
                "Camera permission is required to scan QR codes directly",
                Toast.LENGTH_LONG
            ).show()
        }
    }

    // Auto-request camera permission on screen entry
    LaunchedEffect(Unit) {
        if (!hasCameraPermission) {
            permissionLauncher.launch(Manifest.permission.CAMERA)
        }
    }

    // Laser Animation
    val infiniteTransition = rememberInfiniteTransition(label = "laser")
    val laserOffsetY by infiniteTransition.animateFloat(
        initialValue = 0f,
        targetValue = 220f,
        animationSpec = infiniteRepeatable(
            animation = tween(durationMillis = 1800, easing = LinearEasing),
            repeatMode = RepeatMode.Reverse
        ),
        label = "laser_offset"
    )

    fun resolveCode(rawInput: String) {
        val trimmed = rawInput.trim()
        if (trimmed.isBlank()) {
            errorMessage = "Please enter or scan a valid code."
            return
        }

        val token = when {
            trimmed.contains("/receipt/") -> trimmed.substringAfterLast("/receipt/").substringBefore("?").trim()
            trimmed.startsWith("ORDER-", ignoreCase = true) -> trimmed.substringAfter("ORDER-").trim()
            else -> trimmed
        }

        scope.launch {
            isResolving = true
            errorMessage = null

            // 1. If numeric ID, try fetching by order ID directly
            val numericId = token.toIntOrNull()
            if (numericId != null && numericId > 0) {
                when (val idResult = DinePosApp.instance.orderRepository.getOrderById(numericId)) {
                    is Resource.Success -> {
                        isResolving = false
                        Toast.makeText(context, "Order #${idResult.data.orderNumber} Loaded!", Toast.LENGTH_SHORT).show()
                        onReceiptFound(idResult.data.id, idResult.data.receiptToken)
                        return@launch
                    }
                    else -> {}
                }
            }

            // 2. Otherwise fetch by cryptographic receipt token
            when (val result = DinePosApp.instance.orderRepository.getReceiptByToken(token)) {
                is Resource.Success -> {
                    isResolving = false
                    Toast.makeText(context, "Receipt #${result.data.orderNumber} Verified!", Toast.LENGTH_SHORT).show()
                    onReceiptFound(result.data.id, result.data.receiptToken ?: token)
                }
                is Resource.Error -> {
                    isResolving = false
                    errorMessage = result.message
                }
                else -> {
                    isResolving = false
                }
            }
        }
    }

    Scaffold(
        containerColor = BrandDark,
        topBar = {
            TopAppBar(
                title = { Text("Scan QR Code", color = Color.White, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back", tint = Color.White)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandDark)
            )
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.SpaceBetween
        ) {
            // Header Info
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text(
                    text = if (hasCameraPermission) "Point camera at receipt or menu QR code" else "Camera permission required for scanning",
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = 16.sp,
                    textAlign = TextAlign.Center
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = "Instantly verifies authentic cryptographic receipt tokens",
                    color = TextMuted,
                    fontSize = 12.sp,
                    textAlign = TextAlign.Center
                )
            }

            // Viewfinder Box / Permission Prompt
            if (hasCameraPermission) {
                Box(
                    modifier = Modifier
                        .size(240.dp)
                        .clip(RoundedCornerShape(24.dp))
                        .background(Color(0x22FFFFFF))
                        .border(BorderStroke(2.5.dp, BrandOrange), RoundedCornerShape(24.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.QrCodeScanner,
                        contentDescription = null,
                        tint = Color(0x33FFFFFF),
                        modifier = Modifier.size(120.dp)
                    )

                    // Laser Beam
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(3.dp)
                            .offset(y = (laserOffsetY - 110).dp)
                            .background(
                                Brush.horizontalGradient(
                                    listOf(Color.Transparent, BrandOrange, Color.White, BrandOrange, Color.Transparent)
                                )
                            )
                    )

                    if (isResolving) {
                        CircularProgressIndicator(color = Color.White)
                    }
                }
            } else {
                Card(
                    shape = RoundedCornerShape(24.dp),
                    colors = CardDefaults.cardColors(containerColor = BrandDarkSurface),
                    border = BorderStroke(1.dp, Color(0x33FFFFFF)),
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp)
                ) {
                    Column(
                        modifier = Modifier.padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Icon(
                            imageVector = Icons.Default.CameraAlt,
                            contentDescription = null,
                            tint = BrandOrange,
                            modifier = Modifier.size(48.dp)
                        )
                        Spacer(modifier = Modifier.height(12.dp))
                        Text(
                            text = "Allow Camera Access",
                            color = Color.White,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp
                        )
                        Spacer(modifier = Modifier.height(6.dp))
                        Text(
                            text = "To scan physical receipt QR codes with your device camera, please grant camera permission.",
                            color = TextMuted,
                            fontSize = 12.sp,
                            textAlign = TextAlign.Center
                        )
                        Spacer(modifier = Modifier.height(16.dp))
                        Button(
                            onClick = { permissionLauncher.launch(Manifest.permission.CAMERA) },
                            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                            shape = RoundedCornerShape(12.dp)
                        ) {
                            Text("Grant Permission", fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }

            // Error or Status
            if (!errorMessage.isNullOrBlank()) {
                Surface(
                    color = Color(0x33EF4444),
                    shape = RoundedCornerShape(10.dp),
                    border = BorderStroke(1.dp, StatusError)
                ) {
                    Text(
                        text = errorMessage ?: "",
                        color = Color(0xFFFCA5A5),
                        fontSize = 13.sp,
                        fontWeight = FontWeight.SemiBold,
                        modifier = Modifier.padding(horizontal = 14.dp, vertical = 8.dp)
                    )
                }
            }

            // Manual Token / URL Input Box
            Card(
                shape = RoundedCornerShape(18.dp),
                colors = CardDefaults.cardColors(containerColor = BrandDarkSurface),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(
                        text = "Or enter / paste token directly:",
                        color = TextMuted,
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                    Spacer(modifier = Modifier.height(8.dp))

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        OutlinedTextField(
                            value = manualInput,
                            onValueChange = { manualInput = it },
                            placeholder = { Text("Enter receipt token or order #", fontSize = 12.sp, color = TextMuted) },
                            singleLine = true,
                            shape = RoundedCornerShape(12.dp),
                            keyboardOptions = androidx.compose.foundation.text.KeyboardOptions(
                                imeAction = androidx.compose.ui.text.input.ImeAction.Search
                            ),
                            keyboardActions = androidx.compose.foundation.text.KeyboardActions(
                                onSearch = { resolveCode(manualInput) }
                            ),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedTextColor = Color.White,
                                unfocusedTextColor = Color.White,
                                focusedBorderColor = BrandOrange,
                                unfocusedBorderColor = Color(0x44FFFFFF)
                            ),
                            modifier = Modifier.weight(1f)
                        )

                        Button(
                            onClick = { resolveCode(manualInput) },
                            enabled = manualInput.isNotBlank() && !isResolving,
                            shape = RoundedCornerShape(12.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = BrandOrange)
                        ) {
                            Icon(Icons.Default.Search, contentDescription = "Lookup")
                        }
                    }
                }
            }
        }
    }
}
