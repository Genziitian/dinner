package com.dinepos.app.presentation.navigation

sealed class Screen(val route: String) {
    object Login : Screen("login")
    object CashierBilling : Screen("cashier_billing")
    object CashierSummary : Screen("cashier_summary")
    object ManagerDashboard : Screen("manager_dashboard")
    object ManagerOrders : Screen("manager_orders")
    object OrderDetail : Screen("order_detail/{orderId}") {
        fun createRoute(orderId: Int) = "order_detail/$orderId"
    }
    object ManagerItems : Screen("manager_items")
    object ManagerReports : Screen("manager_reports")
    object Receipt : Screen("receipt/{orderId}?token={token}") {
        fun createRoute(orderId: Int, token: String? = null) = 
            if (!token.isNullOrBlank()) "receipt/$orderId?token=$token" else "receipt/$orderId"
    }
    object QrScanner : Screen("qr_scanner")
    object SuperAdminDashboard : Screen("superadmin_dashboard")
    object AdminRestaurants : Screen("admin_restaurants")
    object AdminUsers : Screen("admin_users")
    object Profile : Screen("profile")
    object MillCreateOrder : Screen("mill_create_order")
    object MillOrders : Screen("mill_orders")
    object MillServices : Screen("mill_services")
    object MillCustomers : Screen("mill_customers")
    object PrivacyPolicy : Screen("privacy_policy")
    object TermsAndConditions : Screen("terms_and_conditions")
}
