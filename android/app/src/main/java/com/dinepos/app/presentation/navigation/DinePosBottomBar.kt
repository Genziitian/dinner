package com.dinepos.app.presentation.navigation

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.slideInVertically
import androidx.compose.animation.slideOutVertically
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.outlined.Assignment
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.outlined.Home
import androidx.compose.material.icons.outlined.Person
import androidx.compose.material.icons.outlined.RestaurantMenu
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.remember
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*

@Composable
fun DinePosBottomBar(
    currentRoute: String?,
    onNavigateToRoute: (String) -> Unit,
    onTakeOrderClick: () -> Unit
) {
    // Only visible on main primary hub screens
    val topLevelRoutes = listOf(
        Screen.ManagerDashboard.route,
        Screen.ManagerOrders.route,
        Screen.ManagerItems.route,
        Screen.ManagerReports.route
    )

    val isVisible = currentRoute in topLevelRoutes

    AnimatedVisibility(
        visible = isVisible,
        enter = slideInVertically(initialOffsetY = { it }),
        exit = slideOutVertically(targetOffsetY = { it })
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .wrapContentHeight()
        ) {
            // Main Bottom Bar Surface
            Surface(
                color = Color.White,
                shadowElevation = 16.dp,
                border = BorderStroke(1.dp, BrandBorder.copy(alpha = 0.5f)),
                shape = RoundedCornerShape(topStart = 20.dp, topEnd = 20.dp),
                modifier = Modifier
                    .fillMaxWidth()
                    .align(Alignment.BottomCenter)
                    .height(68.dp)
            ) {
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 8.dp),
                    horizontalArrangement = Arrangement.SpaceAround,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // 1. Home
                    NavItem(
                        icon = Icons.Outlined.Home,
                        label = "Home",
                        isSelected = currentRoute == Screen.ManagerDashboard.route,
                        onClick = { onNavigateToRoute(Screen.ManagerDashboard.route) },
                        modifier = Modifier.weight(1f)
                    )

                    // 2. Orders
                    NavItem(
                        icon = Icons.AutoMirrored.Outlined.Assignment,
                        label = "Orders",
                        isSelected = currentRoute == Screen.ManagerOrders.route,
                        onClick = { onNavigateToRoute(Screen.ManagerOrders.route) },
                        modifier = Modifier.weight(1f)
                    )

                    // Spacer for Center Floating Action Button
                    Spacer(modifier = Modifier.weight(1.2f))

                    // 4. Menu
                    NavItem(
                        icon = Icons.Outlined.RestaurantMenu,
                        label = "Menu",
                        isSelected = currentRoute == Screen.ManagerItems.route,
                        onClick = { onNavigateToRoute(Screen.ManagerItems.route) },
                        modifier = Modifier.weight(1f)
                    )

                    // 5. Profile / Reports
                    NavItem(
                        icon = Icons.Outlined.Person,
                        label = "Profile",
                        isSelected = currentRoute == Screen.ManagerReports.route,
                        onClick = { onNavigateToRoute(Screen.ManagerReports.route) },
                        modifier = Modifier.weight(1f)
                    )
                }
            }

            // Elevated Center '+' Floating Button (Takes New Order)
            Surface(
                onClick = onTakeOrderClick,
                shape = CircleShape,
                color = BrandOrange,
                shadowElevation = 12.dp,
                border = BorderStroke(3.dp, Color.White),
                modifier = Modifier
                    .size(60.dp)
                    .align(Alignment.TopCenter)
                    .offset(y = (-14).dp)
            ) {
                Box(
                    contentAlignment = Alignment.Center,
                    modifier = Modifier.fillMaxSize()
                ) {
                    Icon(
                        imageVector = Icons.Default.Add,
                        contentDescription = "New Order / POS Billing",
                        tint = Color.White,
                        modifier = Modifier.size(32.dp)
                    )
                }
            }
        }
    }
}

@Composable
private fun NavItem(
    icon: ImageVector,
    label: String,
    isSelected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val interactionSource = remember { MutableInteractionSource() }
    val color = if (isSelected) BrandOrange else Color(0xFF64748B)

    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center,
        modifier = modifier
            .fillMaxHeight()
            .clickable(interactionSource = interactionSource, indication = null) { onClick() }
            .padding(vertical = 4.dp)
    ) {
        Icon(
            imageVector = icon,
            contentDescription = label,
            tint = color,
            modifier = Modifier.size(24.dp)
        )
        Spacer(modifier = Modifier.height(2.dp))
        Text(
            text = label,
            fontSize = 11.sp,
            fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Medium,
            color = color
        )
    }
}
