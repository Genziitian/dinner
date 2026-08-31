package com.dinepos.app.presentation.legal

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
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
                    .widthIn(max = 760.dp)
                    .fillMaxWidth()
                    .verticalScroll(rememberScrollState()),
                shape = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = BrandSurface),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(24.dp)
                ) {
                    Text(
                        text = "PRIVACY POLICY",
                        style = MaterialTheme.typography.headlineSmall,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )
                    Text(
                        text = "Last updated August 30, 2026",
                        style = MaterialTheme.typography.bodySmall,
                        color = TextMuted
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    Text(
                        text = "This Privacy Notice for GI ORDER (\"we,\" \"us,\" or \"our\"), describes how and why we might access, collect, store, use, and/or share (\"process\") your personal information when you use our services (\"Services\"), including when you:\n\n" +
                                "• Visit our website at https://dinner.genziitian.in/ or any website of ours that links to this Privacy Notice\n" +
                                "• Download and use our mobile application (GI ORDER), or any other application of ours that links to this Privacy Notice\n" +
                                "• Use Restraunt Billing Software. Our restaurant management and billing software gives restaurant owners complete control over their daily operations from one secure platform. Owners can register their restaurant, manage billing, track transactions, monitor sales, review audit logs, and oversee staff activities in real time. The system supports role-based access for Owners, Managers, and Cashiers, ensuring each team member only has access to the tools and information required for their role. From billing and transaction management to staff accountability and operational audits, the platform is designed to make restaurant operations simpler, safer, and easier to manage.\n" +
                                "• Engage with us in other related ways, including any marketing or events\n\n" +
                                "Questions or concerns? Reading this Privacy Notice will help you understand your privacy rights and choices. We are responsible for making decisions about how your personal information is processed. If you do not agree with our policies and practices, please do not use our Services. If you still have any questions or concerns, please contact us at pay.laxmikant@gmail.com.\n\n" +
                                "SUMMARY OF KEY POINTS\n" +
                                "This summary provides key points from our Privacy Notice, but you can find out more details about any of these topics by clicking the link following each key point or by using our table of contents below to find the section you are looking for.\n\n" +
                                "What personal information do we process? When you visit, use, or navigate our Services, we may process personal information depending on how you interact with us and the Services, the choices you make, and the products and features you use. Learn more about personal information you disclose to us.\n\n" +
                                "Do we process any sensitive personal information? Some of the information may be considered \"special\" or \"sensitive\" in certain jurisdictions, for example your racial or ethnic origins, sexual orientation, and religious beliefs. We do not process sensitive personal information.\n\n" +
                                "Do we collect any information from third parties? We do not collect any information from third parties.\n\n" +
                                "How do we process your information? We process your information to provide, improve, and administer our Services, communicate with you, for security and fraud prevention, and to comply with law. We may also process your information for other purposes with your consent. We process your information only when we have a valid legal reason to do so. Learn more about how we process your information.\n\n" +
                                "In what situations and with which parties do we share personal information? We may share information in specific situations and with specific third parties. Learn more about when and with whom we share your personal information.\n\n" +
                                "How do we keep your information safe? We have adequate organizational and technical processes and procedures in place to protect your personal information. However, no electronic transmission over the internet or information storage technology can be guaranteed to be 100% secure, so we cannot promise or guarantee that hackers, cybercriminals, or other unauthorized third parties will not be able to defeat our security and improperly collect, access, steal, or modify your information. Learn more about how we keep your information safe.\n\n" +
                                "What are your rights? Depending on where you are located geographically, the applicable privacy law may mean you have certain rights regarding your personal information. Learn more about your privacy rights.\n\n" +
                                "How do you exercise your rights? The easiest way to exercise your rights is by submitting a data subject access request, or by contacting us. We will consider and act upon any request in accordance with applicable data protection laws.\n\n" +
                                "Want to learn more about what we do with any information we collect? Review the Privacy Notice in full.\n\n" +
                                "TABLE OF CONTENTS\n" +
                                "1. WHAT INFORMATION DO WE COLLECT?\n" +
                                "2. HOW DO WE PROCESS YOUR INFORMATION?\n" +
                                "3. WHEN AND WITH WHOM DO WE SHARE YOUR PERSONAL INFORMATION?\n" +
                                "4. HOW LONG DO WE KEEP YOUR INFORMATION?\n" +
                                "5. HOW DO WE KEEP YOUR INFORMATION SAFE?\n" +
                                "6. DO WE COLLECT INFORMATION FROM MINORS?\n" +
                                "7. WHAT ARE YOUR PRIVACY RIGHTS?\n" +
                                "8. CONTROLS FOR DO-NOT-TRACK FEATURES\n" +
                                "9. DO WE MAKE UPDATES TO THIS NOTICE?\n" +
                                "10. HOW CAN YOU CONTACT US ABOUT THIS NOTICE?\n" +
                                "11. HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?\n\n" +
                                "1. WHAT INFORMATION DO WE COLLECT?\n" +
                                "Personal information you disclose to us\n" +
                                "In Short: We collect personal information that you provide to us.\n\n" +
                                "We collect personal information that you voluntarily provide to us when you express an interest in obtaining information about us or our products and Services, when you participate in activities on the Services, or otherwise when you contact us.\n\n" +
                                "Personal Information Provided by You. The personal information that we collect depends on the context of your interactions with us and the Services, the choices you make, and the products and features you use. The personal information we collect may include the following:\n" +
                                "names\n" +
                                "phone numbers\n" +
                                "email addresses\n" +
                                "passwords\n" +
                                "usernames\n" +
                                "contact preferences\n" +
                                "Sensitive Information. We do not process sensitive information.\n\n" +
                                "Application Data. If you use our application(s), we also may collect the following information if you choose to provide us with access or permission:\n" +
                                "Push Notifications. We may request to send you push notifications regarding your account or certain features of the application(s). If you wish to opt out from receiving these types of communications, you may turn them off in your device's settings.\n" +
                                "This information is primarily needed to maintain the security and operation of our application(s), for troubleshooting, and for our internal analytics and reporting purposes.\n\n" +
                                "All personal information that you provide to us must be true, complete, and accurate, and you must notify us of any changes to such personal information.\n\n" +
                                "2. HOW DO WE PROCESS YOUR INFORMATION?\n" +
                                "In Short: We process your information to provide, improve, and administer our Services, communicate with you, for security and fraud prevention, and to comply with law. We may also process your information for other purposes with your consent.\n\n" +
                                "We process your personal information for a variety of reasons, depending on how you interact with our Services, including:\n" +
                                "To deliver and facilitate delivery of services to the user. We may process your information to provide you with the requested service.\n\n" +
                                "3. WHEN AND WITH WHOM DO WE SHARE YOUR PERSONAL INFORMATION?\n" +
                                "In Short: We may share information in specific situations described in this section and/or with the following third parties.\n\n" +
                                "We may need to share your personal information in the following situations:\n" +
                                "Business Transfers. We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.\n\n" +
                                "4. HOW LONG DO WE KEEP YOUR INFORMATION?\n" +
                                "In Short: We keep your information for as long as necessary to fulfill the purposes outlined in this Privacy Notice unless otherwise required by law.\n\n" +
                                "We will only keep your personal information for as long as it is necessary for the purposes set out in this Privacy Notice, unless a longer retention period is required or permitted by law (such as tax, accounting, or other legal requirements). No purpose in this notice will require us keeping your personal information for longer than 2 years.\n\n" +
                                "When we have no ongoing legitimate business need to process your personal information, we will either delete or anonymize such information, or, if this is not possible (for example, because your personal information has been stored in backup archives), then we will securely store your personal information and isolate it from any further processing until deletion is possible.\n\n" +
                                "5. HOW DO WE KEEP YOUR INFORMATION SAFE?\n" +
                                "In Short: We aim to protect your personal information through a system of organizational and technical security measures.\n\n" +
                                "We have implemented appropriate and reasonable technical and organizational security measures designed to protect the security of any personal information we process. However, despite our safeguards and efforts to secure your information, no electronic transmission over the Internet or information storage technology can be guaranteed to be 100% secure, so we cannot promise or guarantee that hackers, cybercriminals, or other unauthorized third parties will not be able to defeat our security and improperly collect, access, steal, or modify your information. Although we will do our best to protect your personal information, transmission of personal information to and from our Services is at your own risk. You should only access the Services within a secure environment.\n\n" +
                                "6. DO WE COLLECT INFORMATION FROM MINORS?\n" +
                                "In Short: We do not knowingly collect data from or market to children under 18 years of age.\n\n" +
                                "We do not knowingly collect, solicit data from, or market to children under 18 years of age, nor do we knowingly sell such personal information. By using the Services, you represent that you are at least 18 or that you are the parent or guardian of such a minor and consent to such minor dependent’s use of the Services. If we learn that personal information from users less than 18 years of age has been collected, we will deactivate the account and take reasonable measures to promptly delete such data from our records. If you become aware of any data we may have collected from children under age 18, please contact us at Pay.laxmikant@gmail.com.\n\n" +
                                "7. WHAT ARE YOUR PRIVACY RIGHTS?\n" +
                                "In Short: You may review, change, or terminate your account at any time, depending on your country, province, or state of residence.\n\n" +
                                "Withdrawing your consent: If we are relying on your consent to process your personal information, which may be express and/or implied consent depending on the applicable law, you have the right to withdraw your consent at any time. You can withdraw your consent at any time by contacting us by using the contact details provided in the section \"HOW CAN YOU CONTACT US ABOUT THIS NOTICE?\" below.\n\n" +
                                "However, please note that this will not affect the lawfulness of the processing before its withdrawal nor, when applicable law allows, will it affect the processing of your personal information conducted in reliance on lawful processing grounds other than consent.\n\n" +
                                "If you have questions or comments about your privacy rights, you may email us at pay.laxmikant@gmail.com.\n\n" +
                                "8. CONTROLS FOR DO-NOT-TRACK FEATURES\n" +
                                "Most web browsers and some mobile operating systems and mobile applications include a Do-Not-Track (\"DNT\") feature or setting you can activate to signal your privacy preference not to have data about your online browsing activities monitored and collected. At this stage, no uniform technology standard for recognizing and implementing DNT signals has been finalized. As such, we do not currently respond to DNT browser signals or any other mechanism that automatically communicates your choice not to be tracked online. If a standard for online tracking is adopted that we must follow in the future, we will inform you about that practice in a revised version of this Privacy Notice.\n\n" +
                                "9. DO WE MAKE UPDATES TO THIS NOTICE?\n" +
                                "In Short: Yes, we will update this notice as necessary to stay compliant with relevant laws.\n\n" +
                                "We may update this Privacy Notice from time to time. The updated version will be indicated by an updated \"Revised\" date at the top of this Privacy Notice. If we make material changes to this Privacy Notice, we may notify you either by prominently posting a notice of such changes or by directly sending you a notification. We encourage you to review this Privacy Notice frequently to be informed of how we are protecting your information.\n\n" +
                                "10. HOW CAN YOU CONTACT US ABOUT THIS NOTICE?\n" +
                                "If you have questions or comments about this notice, you may email us at Pay.laxmikant@gmail.com or contact us by post at:\n\n" +
                                "GI ORDER\n" +
                                "GI ORDER -PATNA\n" +
                                "BIHAR\n" +
                                "PATNA, BIHAR 800001\n" +
                                "India\n\n" +
                                "11. HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?\n" +
                                "Based on the applicable laws of your country, you may have the right to request access to the personal information we collect from you, details about how we have processed it, correct inaccuracies, or delete your personal information. You may also have the right to withdraw your consent to our processing of your personal information. These rights may be limited in some circumstances by applicable law. To request to review, update, or delete your personal information, please fill out and submit a data subject access request.",
                        style = MaterialTheme.typography.bodyMedium,
                        color = BrandDark,
                        lineHeight = 22.sp
                    )

                    Spacer(modifier = Modifier.height(28.dp))

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
                        .padding(24.dp)
                ) {
                    Text(
                        text = "Terms and Conditions",
                        style = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color = BrandDark
                    )

                    Spacer(modifier = Modifier.height(16.dp))

                    Text(
                        text = "WE are a platform only we give you soemthing to store but you are responsible for your data , if data loss happpen we arent responsible for it",
                        style = MaterialTheme.typography.bodyLarge,
                        color = BrandDark,
                        lineHeight = 24.sp
                    )

                    Spacer(modifier = Modifier.height(28.dp))

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
