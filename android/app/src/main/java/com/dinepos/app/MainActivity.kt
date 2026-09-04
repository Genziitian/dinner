package com.dinepos.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.SystemBarStyle
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.WindowInsets
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.dinepos.app.core.network.NetworkMonitor
import com.dinepos.app.core.theme.BrandBackground
import com.dinepos.app.core.theme.DinePosTheme
import com.dinepos.app.presentation.common.NoInternetBanner
import com.dinepos.app.presentation.navigation.DinePosBottomBar
import com.dinepos.app.presentation.navigation.DinePosNavGraph
import com.dinepos.app.presentation.navigation.Screen

class MainActivity : ComponentActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge(
            statusBarStyle = SystemBarStyle.light(
                android.graphics.Color.TRANSPARENT,
                android.graphics.Color.TRANSPARENT
            ),
            navigationBarStyle = SystemBarStyle.light(
                android.graphics.Color.TRANSPARENT,
                android.graphics.Color.TRANSPARENT
            )
        )
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.R) {
            window.setDecorFitsSystemWindows(false)
        }
        window.statusBarColor = android.graphics.Color.TRANSPARENT
        window.navigationBarColor = android.graphics.Color.TRANSPARENT

        val sessionManager = (application as DinePosApp).sessionManager
        val cachedToken = sessionManager.getAuthToken()
        val cachedRole = sessionManager.getUserRole()

        val explicitRoute = intent?.getStringExtra("route")
        val startDestination = if (!explicitRoute.isNullOrBlank()) {
            explicitRoute
        } else if (!cachedToken.isNullOrBlank()) {
            when (cachedRole.lowercase()) {
                "superadmin" -> Screen.SuperAdminDashboard.route
                "cashier" -> Screen.CashierBilling.route
                else -> Screen.ManagerDashboard.route
            }
        } else {
            Screen.Login.route
        }

        setContent {
            val currentLanguage by sessionManager.language.collectAsState()
            val networkMonitor = androidx.compose.runtime.remember { NetworkMonitor(this@MainActivity) }
            val isOnline by networkMonitor.isOnline.collectAsState(initial = networkMonitor.isCurrentlyConnected())

            androidx.compose.runtime.CompositionLocalProvider(
                com.dinepos.app.core.localization.LocalAppLanguage provides currentLanguage
            ) {
                DinePosTheme {
                    Surface(
                        modifier = Modifier.fillMaxSize(),
                        color = BrandBackground
                    ) {
                        val navController = rememberNavController()
                        val navBackStackEntry by navController.currentBackStackEntryAsState()
                        val currentRoute = navBackStackEntry?.destination?.route

                        Scaffold(
                            contentWindowInsets = WindowInsets(0.dp),
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
                                        if (sessionManager.isMill()) {
                                            navController.navigate(Screen.MillCreateOrder.route)
                                        } else {
                                            navController.navigate(Screen.CashierBilling.route)
                                        }
                                    },
                                    onScannerClick = {
                                        navController.navigate(Screen.QrScanner.route)
                                    }
                                )
                            }
                        ) { innerPadding ->
                            Box(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .padding(bottom = innerPadding.calculateBottomPadding())
                            ) {
                                DinePosNavGraph(
                                    navController = navController,
                                    startDestination = startDestination
                                )

                                if (currentRoute != Screen.NoInternet.route) {
                                    NoInternetBanner(
                                        isVisible = !isOnline,
                                        isHi = currentLanguage == "hi",
                                        onRetryClick = {
                                            navController.navigate(Screen.NoInternet.route)
                                        }
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
