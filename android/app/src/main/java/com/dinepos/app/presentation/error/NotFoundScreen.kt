package com.dinepos.app.presentation.error

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Home
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.error.AppErrorCode
import com.dinepos.app.core.localization.LocalAppLanguage
import com.dinepos.app.core.theme.*
import com.dinepos.app.presentation.common.ErrorCodeBadge
import com.dinepos.app.presentation.common.NotFound404Symbol

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NotFoundScreen(
    resourceName: String? = null,
    onNavigateHome: () -> Unit,
    onNavigateBack: (() -> Unit)? = null
) {
    val currentLang = LocalAppLanguage.current
    val isHi = currentLang == "hi"
    val errorCode = AppErrorCode.ERR_HTTP_NOT_FOUND

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
            NotFound404Symbol(size = 140)

            Spacer(modifier = Modifier.height(20.dp))

            // Large 404 text
            Text(
                text = "404",
                style = MaterialTheme.typography.displayMedium,
                fontWeight = FontWeight.Black,
                color = BrandDark,
                letterSpacing = 2.sp
            )

            Spacer(modifier = Modifier.height(6.dp))

            ErrorCodeBadge(
                code = errorCode.code,
                color = Color(0xFF3B82F6)
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

            val description = if (resourceName != null) {
                if (isHi) {
                    "'$resourceName' नहीं मिला। यह रिकॉर्ड हटा दिया गया है या इसका पता बदल गया है।"
                } else {
                    "The item '$resourceName' could not be found. It may have been removed or moved to another section."
                }
            } else {
                errorCode.description(isHi)
            }

            Text(
                text = description,
                style = MaterialTheme.typography.bodyMedium,
                color = TextSecondary,
                textAlign = TextAlign.Center,
                lineHeight = 22.sp
            )

            Spacer(modifier = Modifier.height(32.dp))

            Button(
                onClick = onNavigateHome,
                shape = RoundedCornerShape(14.dp),
                colors = ButtonDefaults.buttonColors(containerColor = BrandOrange),
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp)
            ) {
                Icon(
                    imageVector = Icons.Default.Home,
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
