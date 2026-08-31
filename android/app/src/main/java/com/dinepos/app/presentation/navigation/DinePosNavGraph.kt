package com.dinepos.app.presentation.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.navArgument
import com.dinepos.app.presentation.auth.LoginScreen
import com.dinepos.app.presentation.cashier.BillingScreen
import com.dinepos.app.presentation.items.ManageItemsScreen
import com.dinepos.app.presentation.legal.PrivacyPolicyScreen
import com.dinepos.app.presentation.legal.TermsAndConditionsScreen
import com.dinepos.app.presentation.manager.ManagerDashboardScreen
import com.dinepos.app.presentation.orders.OrderDetailScreen
import com.dinepos.app.presentation.orders.OrderListScreen
import com.dinepos.app.presentation.receipt.ReceiptScreen
import com.dinepos.app.presentation.reports.ReportsScreen
import com.dinepos.app.presentation.summary.SummaryScreen

@Composable
fun DinePosNavGraph(
    navController: NavHostController,
    startDestination: String = Screen.Login.route
) {
    NavHost(
        navController = navController,
        startDestination = startDestination
    ) {
        // 1. Login Screen
        composable(Screen.Login.route) {
            LoginScreen(
                onLoginSuccess = { user ->
                    val destination = if (user.isCashier) {
                        Screen.CashierBilling.route
                    } else {
                        Screen.ManagerDashboard.route
                    }
                    navController.navigate(destination) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                },
                onNavigateToPrivacy = {
                    navController.navigate(Screen.PrivacyPolicy.route)
                },
                onNavigateToTerms = {
                    navController.navigate(Screen.TermsAndConditions.route)
                }
            )
        }

        // 2. Cashier POS Billing Screen
        composable(Screen.CashierBilling.route) {
            BillingScreen(
                onOrderPlaced = { order ->
                    val route = Screen.Receipt.createRoute(order.id, order.receiptToken)
                    navController.navigate(route)
                },
                onNavigateToScanner = {
                    navController.navigate(Screen.QrScanner.route)
                },
                onNavigateToOrders = {
                    navController.navigate(Screen.ManagerOrders.route)
                },
                onNavigateBack = if (navController.previousBackStackEntry != null) {
                    { navController.popBackStack() }
                } else null
            )
        }

        // 3. Cashier Summary Screen
        composable(Screen.CashierSummary.route) {
            SummaryScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }

        // 4. Receipt Screen
        composable(
            route = Screen.Receipt.route,
            arguments = listOf(
                navArgument("orderId") { type = NavType.StringType },
                navArgument("token") {
                    type = NavType.StringType
                    nullable = true
                    defaultValue = null
                }
            )
        ) { backStackEntry ->
            val orderId = backStackEntry.arguments?.getString("orderId")?.toIntOrNull() ?: 0
            val token = backStackEntry.arguments?.getString("token")
            ReceiptScreen(
                orderId = orderId,
                token = token,
                onNavigateBack = { navController.popBackStack() }
            )
        }

        // 5. Manager Dashboard Screen
        composable(Screen.ManagerDashboard.route) {
            ManagerDashboardScreen(
                onNavigateToBilling = { navController.navigate(Screen.CashierBilling.route) },
                onNavigateToOrders = { navController.navigate(Screen.ManagerOrders.route) },
                onNavigateToItems = { navController.navigate(Screen.ManagerItems.route) },
                onNavigateToReports = { navController.navigate(Screen.ManagerReports.route) },
                onNavigateToScanner = { navController.navigate(Screen.QrScanner.route) },
                onOrderClick = { order ->
                    navController.navigate(Screen.OrderDetail.createRoute(order.id))
                },
                onLogout = {
                    navController.navigate(Screen.Login.route) {
                        popUpTo(0) { inclusive = true }
                    }
                }
            )
        }

        // 6. Orders List Screen
        composable(Screen.ManagerOrders.route) {
            OrderListScreen(
                onNavigateBack = { navController.popBackStack() },
                onOrderClick = { order ->
                    navController.navigate(Screen.OrderDetail.createRoute(order.id))
                }
            )
        }

        // 7. Order Detail Screen
        composable(
            route = Screen.OrderDetail.route,
            arguments = listOf(navArgument("orderId") { type = NavType.StringType })
        ) { backStackEntry ->
            val orderId = backStackEntry.arguments?.getString("orderId")?.toIntOrNull() ?: 0
            OrderDetailScreen(
                orderId = orderId,
                onNavigateBack = { navController.popBackStack() },
                onViewReceipt = { id ->
                    navController.navigate(Screen.Receipt.createRoute(id))
                }
            )
        }

        // 8. Menu Items Management Screen
        composable(Screen.ManagerItems.route) {
            ManageItemsScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }

        // 9. Financial Reports Screen
        composable(Screen.ManagerReports.route) {
            ReportsScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }

        // 10. QR Scanner Screen
        composable(Screen.QrScanner.route) {
            com.dinepos.app.presentation.scanner.QrScannerScreen(
                onNavigateBack = { navController.popBackStack() },
                onReceiptFound = { orderId, token ->
                    val route = Screen.Receipt.createRoute(orderId, token)
                    navController.navigate(route) {
                        popUpTo(Screen.QrScanner.route) { inclusive = true }
                    }
                }
            )
        }

        // 11. Privacy Policy Screen
        composable(Screen.PrivacyPolicy.route) {
            PrivacyPolicyScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }

        // 12. Terms and Conditions Screen
        composable(Screen.TermsAndConditions.route) {
            TermsAndConditionsScreen(
                onNavigateBack = { navController.popBackStack() }
            )
        }
    }
}
