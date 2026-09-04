package com.dinepos.app.presentation.navigation

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.slideInVertically
import androidx.compose.animation.slideOutVertically
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.outlined.Assignment
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.QrCodeScanner
import androidx.compose.material.icons.outlined.Assessment
import androidx.compose.material.icons.outlined.GridView
import androidx.compose.material.icons.outlined.Home
import androidx.compose.material.icons.outlined.People
import androidx.compose.material.icons.outlined.Person
import androidx.compose.material.icons.outlined.RestaurantMenu
import androidx.compose.material.icons.outlined.ShoppingCart
import androidx.compose.material.icons.outlined.Storefront
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.remember
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.theme.*

@Composable
fun DinePosBottomBar(
    currentRoute: String?,
    onNavigateToRoute: (String) -> Unit,
    onTakeOrderClick: () -> Unit = {},
    onScannerClick: () -> Unit = {}
) {
    val sessionManager = DinePosApp.instance.sessionManager
    val role = sessionManager.getUserRole().lowercase()

    // All active top level routes per role
    val topLevelRoutes = listOf(
        Screen.SuperAdminDashboard.route,
        Screen.AdminRestaurants.route,
        Screen.AdminUsers.route,
        Screen.ManagerDashboard.route,
        Screen.CashierBilling.route,
        Screen.CashierSummary.route,
        Screen.ManagerOrders.route,
        Screen.ManagerItems.route,
        Screen.ManagerReports.route,
        Screen.Profile.route,
        Screen.MillOrders.route,
        Screen.MillEarnings.route
    )

    val isVisible = currentRoute in topLevelRoutes && currentRoute != Screen.Login.route

    AnimatedVisibility(
        visible = isVisible,
        enter = slideInVertically(initialOffsetY = { it }),
        exit = slideOutVertically(targetOffsetY = { it })
    ) {
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .wrapContentHeight()
                .padding(top = 14.dp)
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
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .navigationBarsPadding()
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(64.dp)
                            .padding(horizontal = 8.dp),
                    horizontalArrangement = Arrangement.SpaceAround,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    when (role) {
                        "superadmin" -> {
                            // Super Admin Navigation (Admin | Restaurants | Users | Profile)
                            NavItem(
                                icon = Icons.Outlined.GridView,
                                label = "Admin",
                                isSelected = currentRoute == Screen.SuperAdminDashboard.route,
                                onClick = { onNavigateToRoute(Screen.SuperAdminDashboard.route) },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.Outlined.Storefront,
                                label = "Restaurants",
                                isSelected = currentRoute == Screen.AdminRestaurants.route,
                                onClick = { onNavigateToRoute(Screen.AdminRestaurants.route) },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.Outlined.People,
                                label = "Users",
                                isSelected = currentRoute == Screen.AdminUsers.route,
                                onClick = { onNavigateToRoute(Screen.AdminUsers.route) },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.Outlined.Person,
                                label = "Profile",
                                isSelected = currentRoute == Screen.Profile.route,
                                onClick = { onNavigateToRoute(Screen.Profile.route) },
                                modifier = Modifier.weight(1f)
                            )
                        }
                        "cashier" -> {
                            // Cashier Navigation (POS | Orders | [Elevated Center Scanner Button] | Shift | Profile)
                            NavItem(
                                icon = Icons.Outlined.ShoppingCart,
                                label = "POS",
                                isSelected = currentRoute == Screen.CashierBilling.route,
                                onClick = { onNavigateToRoute(Screen.CashierBilling.route) },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.AutoMirrored.Outlined.Assignment,
                                label = "Orders",
                                isSelected = currentRoute == Screen.ManagerOrders.route,
                                onClick = { onNavigateToRoute(Screen.ManagerOrders.route) },
                                modifier = Modifier.weight(1f)
                            )
                            Spacer(modifier = Modifier.weight(1.2f))
                            NavItem(
                                icon = Icons.Outlined.Assessment,
                                label = "Shift",
                                isSelected = currentRoute == Screen.CashierSummary.route,
                                onClick = { onNavigateToRoute(Screen.CashierSummary.route) },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.Outlined.Person,
                                label = "Profile",
                                isSelected = currentRoute == Screen.Profile.route,
                                onClick = { onNavigateToRoute(Screen.Profile.route) },
                                modifier = Modifier.weight(1f)
                            )
                        }
                        else -> {
                            val isMill = sessionManager.isMill()
                            val isHi = com.dinepos.app.core.localization.LocalAppLanguage.current == "hi"
                            // Manager Navigation (Home | Orders | [Elevated Center + Button] | Menu/Rates | Profile)
                            NavItem(
                                icon = Icons.Outlined.Home,
                                label = com.dinepos.app.core.localization.L10n.navHome(isHi),
                                isSelected = currentRoute == Screen.ManagerDashboard.route,
                                onClick = { onNavigateToRoute(Screen.ManagerDashboard.route) },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.AutoMirrored.Outlined.Assignment,
                                label = com.dinepos.app.core.localization.L10n.navOrders(isHi),
                                isSelected = currentRoute == (if (isMill) Screen.MillOrders.route else Screen.ManagerOrders.route),
                                onClick = {
                                    if (isMill) {
                                        onNavigateToRoute(Screen.MillOrders.route)
                                    } else {
                                        onNavigateToRoute(Screen.ManagerOrders.route)
                                    }
                                },
                                modifier = Modifier.weight(1f)
                            )
                            Spacer(modifier = Modifier.weight(1.2f))
                            NavItem(
                                icon = if (isMill) Icons.Outlined.Assessment else Icons.Outlined.RestaurantMenu,
                                label = if (isMill) com.dinepos.app.core.localization.L10n.navEarning(isHi) else (if (isHi) "मेनू" else "Menu"),
                                isSelected = currentRoute == (if (isMill) Screen.MillEarnings.route else Screen.ManagerItems.route),
                                onClick = {
                                    if (isMill) {
                                        onNavigateToRoute(Screen.MillEarnings.route)
                                    } else {
                                        onNavigateToRoute(Screen.ManagerItems.route)
                                    }
                                },
                                modifier = Modifier.weight(1f)
                            )
                            NavItem(
                                icon = Icons.Outlined.Person,
                                label = com.dinepos.app.core.localization.L10n.navProfile(isHi),
                                isSelected = currentRoute == Screen.Profile.route,
                                onClick = { onNavigateToRoute(Screen.Profile.route) },
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }
                }
            }
        }

            // Elevated Center Floating Action Button
            if (role == "cashier") {
                // Elevated QR Scanner Button for Cashiers
                Surface(
                    onClick = onScannerClick,
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
                            imageVector = Icons.Default.QrCodeScanner,
                            contentDescription = "Scan QR Order Receipt",
                            tint = Color.White,
                            modifier = Modifier.size(28.dp)
                        )
                    }
                }
            } else if (role == "manager") {
                // Elevated '+' New Order / POS Billing Button for Managers
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
