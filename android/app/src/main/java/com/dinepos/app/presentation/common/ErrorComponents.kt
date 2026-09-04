package com.dinepos.app.presentation.common

import androidx.compose.animation.*
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.error.AppErrorCode
import com.dinepos.app.core.theme.*

/**
 * Visual Symbol for No Internet Connection.
 */
@Composable
fun NoInternetSymbol(
    modifier: Modifier = Modifier,
    size: Int = 110
) {
    Box(
        modifier = modifier.size(size.dp),
        contentAlignment = Alignment.Center
    ) {
        // Outer soft pulsing halo
        Box(
            modifier = Modifier
                .size(size.dp)
                .clip(CircleShape)
                .background(
                    Brush.radialGradient(
                        colors = listOf(
                            BrandOrange.copy(alpha = 0.18f),
                            BrandOrange.copy(alpha = 0.05f),
                            Color.Transparent
                        )
                    )
                )
        )
        // Center icon container
        Surface(
            shape = CircleShape,
            color = BrandOrange.copy(alpha = 0.12f),
            border = BorderStroke(2.dp, BrandOrange.copy(alpha = 0.35f)),
            modifier = Modifier.size((size * 0.72).dp)
        ) {
            Box(contentAlignment = Alignment.Center) {
                Icon(
                    imageVector = Icons.Default.WifiOff,
                    contentDescription = "No Internet Connection",
                    tint = BrandOrange,
                    modifier = Modifier.size((size * 0.38).dp)
                )
            }
        }
    }
}

/**
 * Visual Symbol for 404 Not Found.
 */
@Composable
fun NotFound404Symbol(
    modifier: Modifier = Modifier,
    size: Int = 110
) {
    Box(
        modifier = modifier.size(size.dp),
        contentAlignment = Alignment.Center
    ) {
        Box(
            modifier = Modifier
                .size(size.dp)
                .clip(CircleShape)
                .background(
                    Brush.radialGradient(
                        colors = listOf(
                            Color(0xFF3B82F6).copy(alpha = 0.18f),
                            Color(0xFF3B82F6).copy(alpha = 0.04f),
                            Color.Transparent
                        )
                    )
                )
        )
        Surface(
            shape = CircleShape,
            color = Color(0xFF3B82F6).copy(alpha = 0.12f),
            border = BorderStroke(2.dp, Color(0xFF3B82F6).copy(alpha = 0.35f)),
            modifier = Modifier.size((size * 0.72).dp)
        ) {
            Box(contentAlignment = Alignment.Center) {
                Icon(
                    imageVector = Icons.Outlined.SearchOff,
                    contentDescription = "Not Found",
                    tint = Color(0xFF3B82F6),
                    modifier = Modifier.size((size * 0.38).dp)
                )
            }
        }
    }
}

/**
 * Visual Symbol for Generic or Server Errors.
 */
@Composable
fun GenericErrorSymbol(
    modifier: Modifier = Modifier,
    size: Int = 110,
    color: Color = StatusError
) {
    Box(
        modifier = modifier.size(size.dp),
        contentAlignment = Alignment.Center
    ) {
        Box(
            modifier = Modifier
                .size(size.dp)
                .clip(CircleShape)
                .background(
                    Brush.radialGradient(
                        colors = listOf(
                            color.copy(alpha = 0.18f),
                            color.copy(alpha = 0.04f),
                            Color.Transparent
                        )
                    )
                )
        )
        Surface(
            shape = CircleShape,
            color = color.copy(alpha = 0.12f),
            border = BorderStroke(2.dp, color.copy(alpha = 0.35f)),
            modifier = Modifier.size((size * 0.72).dp)
        ) {
            Box(contentAlignment = Alignment.Center) {
                Icon(
                    imageVector = Icons.Outlined.ErrorOutline,
                    contentDescription = "Error",
                    tint = color,
                    modifier = Modifier.size((size * 0.38).dp)
                )
            }
        }
    }
}

/**
 * Reusable Error Code Badge displaying standard technical code e.g. ERR_NET_001.
 */
@Composable
fun ErrorCodeBadge(
    code: String,
    modifier: Modifier = Modifier,
    color: Color = TextSecondary
) {
    Surface(
        shape = RoundedCornerShape(8.dp),
        color = color.copy(alpha = 0.08f),
        border = BorderStroke(1.dp, color.copy(alpha = 0.25f)),
        modifier = modifier
    ) {
        Row(
            modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(6.dp)
        ) {
            Box(
                modifier = Modifier
                    .size(6.dp)
                    .background(color, CircleShape)
            )
            Text(
                text = "CODE: $code",
                fontSize = 11.5.sp,
                fontWeight = FontWeight.Bold,
                fontFamily = FontFamily.Monospace,
                color = color,
                letterSpacing = 0.8.sp
            )
        }
    }
}

/**
 * Persistent Top Offline Banner when internet is lost.
 */
@Composable
fun NoInternetBanner(
    isVisible: Boolean,
    isHi: Boolean = false,
    onRetryClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    AnimatedVisibility(
        visible = isVisible,
        enter = slideInVertically(initialOffsetY = { -it }) + fadeIn(),
        exit = slideOutVertically(targetOffsetY = { -it }) + fadeOut(),
        modifier = modifier
    ) {
        Surface(
            color = StatusError,
            modifier = Modifier.fillMaxWidth()
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .statusBarsPadding()
                    .padding(horizontal = 16.dp, vertical = 8.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                    modifier = Modifier.weight(1f)
                ) {
                    Icon(
                        imageVector = Icons.Default.WifiOff,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(18.dp)
                    )
                    Text(
                        text = if (isHi) "आप ऑफ़लाइन हैं (ERR_NET_001)" else "You are offline (ERR_NET_001)",
                        color = Color.White,
                        fontWeight = FontWeight.Bold,
                        fontSize = 12.5.sp
                    )
                }

                Surface(
                    onClick = onRetryClick,
                    shape = RoundedCornerShape(6.dp),
                    color = Color.White.copy(alpha = 0.2f),
                    modifier = Modifier.padding(start = 8.dp)
                ) {
                    Text(
                        text = if (isHi) "पुनः प्रयास करें" else "Retry",
                        color = Color.White,
                        fontSize = 11.5.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp)
                    )
                }
            }
        }
    }
}

/**
 * Reusable Embeddable Error Card for screen sections or failed data lists.
 */
@Composable
fun ErrorStateCard(
    errorCode: AppErrorCode,
    isHi: Boolean = false,
    onRetry: (() -> Unit)? = null,
    modifier: Modifier = Modifier
) {
    Card(
        shape = RoundedCornerShape(18.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        border = BorderStroke(1.dp, BrandBorder),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
        modifier = modifier.fillMaxWidth()
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            when (errorCode) {
                AppErrorCode.ERR_NO_INTERNET -> NoInternetSymbol(size = 80)
                AppErrorCode.ERR_HTTP_NOT_FOUND -> NotFound404Symbol(size = 80)
                else -> GenericErrorSymbol(size = 80)
            }

            Spacer(modifier = Modifier.height(14.dp))

            ErrorCodeBadge(code = errorCode.code)

            Spacer(modifier = Modifier.height(10.dp))

            Text(
                text = errorCode.title(isHi),
                fontWeight = FontWeight.Bold,
                fontSize = 16.sp,
                color = BrandDark,
                textAlign = TextAlign.Center
            )

            Spacer(modifier = Modifier.height(6.dp))

            Text(
                text = errorCode.description(isHi),
                fontSize = 12.5.sp,
                color = TextSecondary,
                textAlign = TextAlign.Center,
                lineHeight = 18.sp
            )

            if (onRetry != null) {
                Spacer(modifier = Modifier.height(16.dp))
                Button(
                    onClick = onRetry,
                    colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                    shape = RoundedCornerShape(10.dp),
                    contentPadding = PaddingValues(horizontal = 20.dp, vertical = 10.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.Refresh,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(16.dp)
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = errorCode.action(isHi),
                        fontWeight = FontWeight.Bold,
                        fontSize = 13.sp,
                        color = Color.White
                    )
                }
            }
        }
    }
}
