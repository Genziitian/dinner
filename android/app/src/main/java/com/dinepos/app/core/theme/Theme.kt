package com.dinepos.app.core.theme

import android.app.Activity
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.runtime.SideEffect
import androidx.compose.ui.graphics.toArgb
import androidx.compose.ui.platform.LocalView
import androidx.core.view.WindowCompat

private val LightColorScheme = lightColorScheme(
    primary = BrandOrange,
    onPrimary = BrandSurface,
    primaryContainer = BrandOrangeLight,
    onPrimaryContainer = BrandOrangeDark,
    secondary = BrandDark,
    onSecondary = BrandSurface,
    secondaryContainer = BrandDarkSurface,
    onSecondaryContainer = BrandSurface,
    tertiary = BrandEmerald,
    onTertiary = BrandSurface,
    background = BrandBackground,
    onBackground = TextPrimary,
    surface = BrandSurface,
    onSurface = TextPrimary,
    surfaceVariant = BrandBackground,
    onSurfaceVariant = TextSecondary,
    outline = BrandBorder,
    error = StatusError,
    onError = BrandSurface
)

private val DarkColorScheme = darkColorScheme(
    primary = BrandOrange,
    onPrimary = BrandSurface,
    primaryContainer = BrandOrangeDark,
    onPrimaryContainer = BrandOrangeLight,
    secondary = BrandDarkSurface,
    onSecondary = BrandSurface,
    background = BrandDark,
    onBackground = BrandSurface,
    surface = BrandDarkSurface,
    onSurface = BrandSurface,
    surfaceVariant = BrandDark,
    onSurfaceVariant = TextMuted,
    outline = BrandDarkSurface,
    error = StatusError,
    onError = BrandSurface
)

@Composable
fun DinePosTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    content: @Composable () -> Unit
) {
    val colorScheme = if (darkTheme) DarkColorScheme else LightColorScheme
    val view = LocalView.current
    if (!view.isInEditMode) {
        SideEffect {
            val window = (view.context as Activity).window
            WindowCompat.getInsetsController(window, view).isAppearanceLightStatusBars = !darkTheme
        }
    }

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography,
        content = content
    )
}
