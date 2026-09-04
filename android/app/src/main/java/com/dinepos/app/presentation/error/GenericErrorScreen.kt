package com.dinepos.app.presentation.error

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.widget.Toast
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.ContentCopy
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.error.AppErrorCode
import com.dinepos.app.core.localization.LocalAppLanguage
import com.dinepos.app.core.theme.*
import com.dinepos.app.presentation.common.ErrorCodeBadge
import com.dinepos.app.presentation.common.GenericErrorSymbol
import com.dinepos.app.presentation.common.NoInternetSymbol
import com.dinepos.app.presentation.common.NotFound404Symbol

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GenericErrorScreen(
    errorCode: AppErrorCode = AppErrorCode.ERR_UNKNOWN,
    technicalDetails: String? = null,
    onRetry: (() -> Unit)? = null,
    onNavigateHome: () -> Unit = {},
    onNavigateBack: (() -> Unit)? = null
) {
    val context = LocalContext.current
    val currentLang = LocalAppLanguage.current
    val isHi = currentLang == "hi"

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
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 28.dp, vertical = 20.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            when (errorCode) {
                AppErrorCode.ERR_NO_INTERNET -> NoInternetSymbol(size = 130)
                AppErrorCode.ERR_HTTP_NOT_FOUND -> NotFound404Symbol(size = 130)
                else -> GenericErrorSymbol(size = 130, color = StatusError)
            }

            Spacer(modifier = Modifier.height(20.dp))

            ErrorCodeBadge(
                code = errorCode.code,
                color = if (errorCode == AppErrorCode.ERR_NO_INTERNET) BrandOrange else StatusError
            )

            Spacer(modifier = Modifier.height(14.dp))

            Text(
                text = errorCode.title(isHi),
                style = MaterialTheme.typography.headlineSmall,
                fontWeight = FontWeight.Bold,
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

            // Optional technical details expandable/box
            if (!technicalDetails.isNullOrBlank()) {
                Spacer(modifier = Modifier.height(18.dp))
                Surface(
                    color = BrandDark.copy(alpha = 0.04f),
                    shape = RoundedCornerShape(12.dp),
                    border = BorderStroke(1.dp, BrandBorder),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier.padding(14.dp)
                    ) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = if (isHi) "तकनीकी विवरण" else "Diagnostic Details",
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                color = TextSecondary
                            )
                            IconButton(
                                onClick = {
                                    val clipboard = context.getSystemService(Context.CLIPBOARD_SERVICE) as ClipboardManager
                                    val clip = ClipData.newPlainText("Error Details", "[${errorCode.code}] $technicalDetails")
                                    clipboard.setPrimaryClip(clip)
                                    Toast.makeText(
                                        context,
                                        if (isHi) "त्रुटि कोड कॉपी किया गया" else "Error details copied to clipboard",
                                        Toast.LENGTH_SHORT
                                    ).show()
                                },
                                modifier = Modifier.size(24.dp)
                            ) {
                                Icon(
                                    imageVector = Icons.Default.ContentCopy,
                                    contentDescription = "Copy Error Details",
                                    tint = TextSecondary,
                                    modifier = Modifier.size(16.dp)
                                )
                            }
                        }
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = technicalDetails,
                            fontFamily = FontFamily.Monospace,
                            fontSize = 11.sp,
                            color = BrandDark.copy(alpha = 0.8f),
                            lineHeight = 16.sp,
                            maxLines = 4
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(28.dp))

            if (onRetry != null) {
                Button(
                    onClick = onRetry,
                    shape = RoundedCornerShape(14.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp)
                ) {
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
                Spacer(modifier = Modifier.height(12.dp))
            }

            Button(
                onClick = onNavigateHome,
                shape = RoundedCornerShape(14.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = if (onRetry == null) BrandOrange else BrandDark
                ),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(50.dp)
            ) {
                Icon(
                    imageVector = Icons.Default.Home,
                    contentDescription = null,
                    tint = Color.White,
                    modifier = Modifier.size(18.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                Text(
                    text = if (isHi) "डैशबोर्ड पर लौटें" else "Return to Dashboard",
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color.White
                )
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
