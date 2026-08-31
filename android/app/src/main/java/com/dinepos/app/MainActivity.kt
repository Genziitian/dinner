package com.dinepos.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.dinepos.app.core.theme.BrandBackground
import com.dinepos.app.core.theme.DinePosTheme
import com.dinepos.app.presentation.navigation.DinePosBottomBar
import com.dinepos.app.presentation.navigation.DinePosNavGraph
import com.dinepos.app.presentation.navigation.Screen

class MainActivity : ComponentActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.R) {
            window.setDecorFitsSystemWindows(false)
        }
        window.statusBarColor = android.graphics.Color.TRANSPARENT
        window.navigationBarColor = android.graphics.Color.TRANSPARENT

        val sessionManager = (application as DinePosApp).sessionManager
        val cachedToken = sessionManager.getAuthToken()
        val cachedRole = sessionManager.getUserRole()

        val startDestination = if (!cachedToken.isNullOrBlank()) {
            when (cachedRole.lowercase()) {
                "superadmin" -> Screen.SuperAdminDashboard.route
                "cashier" -> Screen.CashierBilling.route
                else -> Screen.ManagerDashboard.route
            }
        } else {
            Screen.Login.route
        }

        setContent {
            DinePosTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = BrandBackground
                ) {
                    val navController = rememberNavController()
                    val navBackStackEntry by navController.currentBackStackEntryAsState()
                    val currentRoute = navBackStackEntry?.destination?.route

                    Scaffold(
                        bottomBar = {
                            DinePosBottomBar(
                                currentRoute = currentRoute,
                                onNavigateToRoute = { route ->
                                    navController.navigate(route) {
                                        popUpTo(navController.graph.startDestinationId) {
                                            saveState = true
                                        }
                                        launchSingleTop = true
                                        restoreState = true
                                    }
                                },
                                onTakeOrderClick = {
                                    navController.navigate(Screen.CashierBilling.route)
                                },
                                onScannerClick = {
                                    navController.navigate(Screen.QrScanner.route)
                                }
                            )
                        }
                    ) { innerPadding ->
                        Box(modifier = Modifier.padding(innerPadding)) {
                            DinePosNavGraph(
                                navController = navController,
                                startDestination = startDestination
                            )
                        }
                    }
                }
            }
        }
    }
}
