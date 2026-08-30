package com.dinepos.app

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.Surface
import androidx.compose.ui.Modifier
import androidx.navigation.compose.rememberNavController
import com.dinepos.app.core.theme.BrandBackground
import com.dinepos.app.core.theme.DinePosTheme
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
            if (cachedRole == "cashier") {
                Screen.CashierBilling.route
            } else {
                Screen.ManagerDashboard.route
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
                    DinePosNavGraph(
                        navController = navController,
                        startDestination = startDestination
                    )
                }
            }
        }
    }
}
