package com.dinepos.app.presentation.error

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.error.AppErrorCode
import com.dinepos.app.core.localization.LocalAppLanguage
import com.dinepos.app.core.network.NetworkMonitor
import com.dinepos.app.core.theme.*
import com.dinepos.app.presentation.common.ErrorCodeBadge
import com.dinepos.app.presentation.common.NoInternetSymbol
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NoInternetScreen(
    onRetrySuccess: () -> Unit = {},
    onNavigateBack: (() -> Unit)? = null
) {
    val context = LocalContext.current
    val currentLang = LocalAppLanguage.current
    val isHi = currentLang == "hi"
    val scope = rememberCoroutineScope()
    val networkMonitor = remember { NetworkMonitor(context) }

    var isChecking by remember { mutableStateOf(false) }
    var connectionFailedNotice by remember { mutableStateOf(false) }

    val errorCode = AppErrorCode.ERR_NO_INTERNET

    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {},
                navigationIcon = {
                    if (onNavigateBack != null) {
                        IconButton(onClick = onNavigateBack) {
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                                contentDescription = "Back",
                                tint = BrandDark
                            )
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.Transparent)
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .padding(horizontal = 28.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            NoInternetSymbol(size = 140)

            Spacer(modifier = Modifier.height(24.dp))

            ErrorCodeBadge(
                code = errorCode.code,
                color = BrandOrange
            )

            Spacer(modifier = Modifier.height(14.dp))

            Text(
                text = errorCode.title(isHi),
                style = MaterialTheme.typography.headlineMedium,
                fontWeight = FontWeight.Black,
                color = BrandDark,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(10.dp))

            Text(
                text = errorCode.description(isHi),
                style = MaterialTheme.typography.bodyMedium,
                color = TextSecondary,
                textAlign = TextAlign.Center,
                lineHeight = 22.sp
            )

            if (connectionFailedNotice) {
                Spacer(modifier = Modifier.height(16.dp))
                Surface(
                    color = StatusError.copy(alpha = 0.08f),
                    shape = RoundedCornerShape(10.dp),
                    border = androidx.compose.foundation.BorderStroke(1.dp, StatusError.copy(alpha = 0.3f))
                ) {
                    Text(
                        text = if (isHi) "कनेक्शन अभी भी उपलब्ध नहीं है। कृपया नेटवर्क ऑन करें।" else "Network connection is still unavailable. Please check settings.",
                        color = StatusError,
                        fontSize = 12.sp,
                        fontWeight = FontWeight.SemiBold,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.padding(horizontal = 14.dp, vertical = 8.dp)
                    )
                }
            }

            Spacer(modifier = Modifier.height(32.dp))

            // Retry Button
            Button(
                onClick = {
                    if (!isChecking) {
                        isChecking = true
                        connectionFailedNotice = false
                        scope.launch {
                            delay(600)
                            val isConnected = networkMonitor.checkServerConnection()
                            isChecking = false
                            if (isConnected) {
                                onRetrySuccess()
                            } else {
                                connectionFailedNotice = true
                            }
                        }
                    }
                },
                enabled = !isChecking,
                shape = RoundedCornerShape(14.dp),
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
            ) {
                if (isChecking) {
                    CircularProgressIndicator(
                        color = Color.White,
                        modifier = Modifier.size(22.dp),
                        strokeWidth = 2.5.dp
                    )
                } else {
                    Icon(
                        imageVector = Icons.Default.Refresh,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(20.dp)
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(
                        text = errorCode.action(isHi),
                        fontSize = 15.sp,
                        fontWeight = FontWeight.Bold,
                        color = Color.White
                    )
                }
            }

            if (onNavigateBack != null) {
                Spacer(modifier = Modifier.height(12.dp))
                OutlinedButton(
                    onClick = onNavigateBack,
                    shape = RoundedCornerShape(14.dp),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(50.dp)
                ) {
                    Text(
                        text = if (isHi) "पीछे जाएं (Go Back)" else "Go Back",
                        color = TextSecondary,
                        fontWeight = FontWeight.SemiBold,
                        fontSize = 14.sp
                    )
                }
            }
        }
    }
}
