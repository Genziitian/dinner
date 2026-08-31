package com.dinepos.app.presentation.legal

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.Security
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dinepos.app.core.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PrivacyPolicyScreen(
    onNavigateBack: () -> Unit
) {
    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Privacy Policy",
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(
                            imageVector = Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = BrandDark
                        )
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
            )
        }
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp),
            contentAlignment = Alignment.TopCenter
        ) {
            Card(
                modifier = Modifier
                    .widthIn(max = 680.dp)
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState()),
                shape = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(20.dp)
                ) {
                    // Header Tag
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Security,
                            contentDescription = null,
                            tint = BrandOrange,
                            modifier = Modifier.size(24.dp)
                        )
                        Text(
                            text = "DinePOS Privacy Policy",
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    }

                    Spacer(modifier = Modifier.height(16.dp))

                    // Primary Highlight Box
                    Surface(
                        shape = RoundedCornerShape(12.dp),
                        color = Color(0xFFF1F5F9),
                        border = BorderStroke(1.dp, BrandBorder),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Text(
                                text = "Our restaurant management and billing software gives restaurant owners complete control over their daily operations from one secure platform.\n\n" +
                                        "Owners can register their restaurant, manage billing, track transactions, monitor sales, review audit logs, and oversee staff activities in real time. The system supports role-based access for Owners, Managers, and Cashiers, ensuring each team member only has access to the tools and information required for their role.\n\n" +
                                        "From billing and transaction management to staff accountability and operational audits, the platform is designed to make restaurant operations simpler, safer, and easier to manage.",
                                style = MaterialTheme.typography.bodyMedium,
                                color = BrandDark,
                                lineHeight = 22.sp
                            )
                        }
                    }

                    Spacer(modifier = Modifier.height(20.dp))

                    // 1. Role-Based Access
                    Text(
                        text = "1. Role-Based Access & Data Segregation",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "• Owners / Admins: Full administrative oversight including billing settings, menu setup, sales reports, user management, and real-time audit logs.\n" +
                                "• Managers: Management of daily menu items, order monitoring, receipts, and cashier accountability.\n" +
                                "• Cashiers: Dedicated fast point-of-sale checkout, live cart management, receipt printing, and personal shift summary.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // 2. Information Collected
                    Text(
                        text = "2. Information Processed",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "We process authentication credentials, restaurant orders, timestamped transaction entries, payment method categorizations, and operational audit trails. Optional customer details entered during checkout are solely used for receipt delivery. We do not sell or monetize personal data.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // 3. Data Protection
                    Text(
                        text = "3. Security & Cryptographic Integrity",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "Communications between the mobile application and server utilize encrypted TLS/HTTPS protocols. On Android devices, authentication tokens are saved in encrypted key-backed storage. Receipt verification utilizes cryptographic digital tokens.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(24.dp))

                    Button(
                        onClick = onNavigateBack,
                        shape = RoundedCornerShape(12.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = BrandDark),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(text = "Close & Return", color = Color.White, fontWeight = FontWeight.Bold)
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TermsAndConditionsScreen(
    onNavigateBack: () -> Unit
) {
    Scaffold(
        containerColor = BrandBackground,
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text = "Terms & Conditions",
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(
                            imageVector = Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = BrandDark
                        )
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = BrandSurface)
            )
        }
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(16.dp),
            contentAlignment = Alignment.TopCenter
        ) {
            Card(
                modifier = Modifier
                    .widthIn(max = 680.dp)
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState()),
                shape = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(20.dp)
                ) {
                    // Header Tag
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Info,
                            contentDescription = null,
                            tint = BrandOrange,
                            modifier = Modifier.size(24.dp)
                        )
                        Text(
                            text = "Terms & Conditions",
                            style = MaterialTheme.typography.titleLarge,
                            fontWeight = FontWeight.Bold,
                            color = BrandDark
                        )
                    }

                    Spacer(modifier = Modifier.height(16.dp))

                    // Mandatory Platform & Data Disclaimer
                    Surface(
                        shape = RoundedCornerShape(12.dp),
                        color = Color(0xFFFFFBEB),
                        border = BorderStroke(1.dp, Color(0xFFFDE68A)),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier.padding(16.dp),
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            Icon(
                                imageVector = Icons.Default.Warning,
                                contentDescription = null,
                                tint = BrandAmber,
                                modifier = Modifier.size(24.dp)
                            )
                            Column {
                                Text(
                                    text = "Platform Data Disclaimer",
                                    fontWeight = FontWeight.Bold,
                                    color = Color(0xFF92400E),
                                    style = MaterialTheme.typography.titleSmall
                                )
                                Spacer(modifier = Modifier.height(4.dp))
                                Text(
                                    text = "We are a platform only. We provide you tools and software to store, manage, and process your restaurant records, but you are solely responsible for your data. If data loss happens, we are not responsible for it.",
                                    style = MaterialTheme.typography.bodyMedium,
                                    color = Color(0xFF92400E),
                                    fontWeight = FontWeight.Medium,
                                    lineHeight = 20.sp
                                )
                            }
                        }
                    }

                    Spacer(modifier = Modifier.height(20.dp))

                    // 1. Service Scope
                    Text(
                        text = "1. Service Scope & Provision",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "DinePOS is provided as an operational billing and management platform for restaurants. By accessing the web or mobile applications, you agree to comply with all applicable terms.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // 2. Data Responsibility & Backups
                    Text(
                        text = "2. User Data & Backups",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "Restaurant owners and managers are responsible for routinely exporting their reports, archiving financial receipts, and maintaining their own offline backups. DinePOS is not liable for data loss caused by system failures, offline discrepancies, or device loss.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // 3. User Credentials & Account Security
                    Text(
                        text = "3. Account Security & Role Segregation",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "You are responsible for keeping all staff credentials confidential and assigning appropriate roles to prevent unauthorized access, discounts, or order adjustments.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    // 4. Limitation of Liability
                    Text(
                        text = "4. Limitation of Liability",
                        style = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    Text(
                        text = "Under no circumstances shall DinePOS or its contributors be held liable for indirect, incidental, or consequential damages resulting from downtime, network errors, data corruption, or operational disruptions.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = TextSecondary,
                        lineHeight = 20.sp
                    )

                    Spacer(modifier = Modifier.height(24.dp))

                    Button(
                        onClick = onNavigateBack,
                        shape = RoundedCornerShape(12.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = BrandDark),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text(text = "Close & Return", color = Color.White, fontWeight = FontWeight.Bold)
                    }
                }
            }
        }
    }
}
