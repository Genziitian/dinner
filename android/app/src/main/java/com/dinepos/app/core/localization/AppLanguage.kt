package com.dinepos.app.core.localization

import androidx.compose.runtime.Composable
import androidx.compose.runtime.compositionLocalOf

val LocalAppLanguage = compositionLocalOf { "en" }

@Composable
fun isHindi(): Boolean = LocalAppLanguage.current == "hi"

@Composable
fun tr(en: String, hi: String): String = if (isHindi()) hi else en

object L10n {
    // Navigation
    fun navHome(isHi: Boolean) = if (isHi) "होम" else "Home"
    fun navOrders(isHi: Boolean) = if (isHi) "ऑर्डर्स" else "Orders"
    fun navEarning(isHi: Boolean) = if (isHi) "कमाई" else "Earning"
    fun navRates(isHi: Boolean) = if (isHi) "दरें" else "Rates"
    fun navProfile(isHi: Boolean) = if (isHi) "प्रोफ़ाइल" else "Profile"

    // Dashboard
    fun todayMillRevenue(isHi: Boolean) = if (isHi) "आज की मिल कमाई" else "TODAY'S MILL REVENUE"
    fun todayRevenue(isHi: Boolean) = if (isHi) "आज की कुल बिक्री" else "TODAY'S REVENUE"
    fun paid(isHi: Boolean) = if (isHi) "जमा" else "Paid"
    fun pending(isHi: Boolean) = if (isHi) "बकाया" else "Pending"
    fun quickActions(isHi: Boolean) = if (isHi) "त्वरित कार्य" else "QUICK ACTIONS"
    fun grindingRates(isHi: Boolean) = if (isHi) "पिसाई दरें" else "Grinding Rates"
    fun grindingRatesSub(isHi: Boolean) = if (isHi) "प्रति किलो दर" else "Price per KG"
    fun customers(isHi: Boolean) = if (isHi) "ग्राहक सूची" else "Customers"
    fun customersSub(isHi: Boolean) = if (isHi) "खाता और बकाया" else "Directory & Dues"
    fun recentOrders(isHi: Boolean) = if (isHi) "हाल के ऑर्डर्स" else "RECENT ORDERS"
    fun viewAll(isHi: Boolean) = if (isHi) "सभी देखें" else "View All"
    fun walkInCustomer(isHi: Boolean) = if (isHi) "काउंटर ग्राहक" else "Walk-in Customer"

    // Orders Screen
    fun grindingOrdersTitle(isHi: Boolean) = if (isHi) "पिसाई ऑर्डर्स" else "Grinding Orders"
    fun searchOrders(isHi: Boolean) = if (isHi) "ऑर्डर #, नाम या मोबाइल खोजें..." else "Search order #, ID, name, or phone..."
    fun today(isHi: Boolean) = if (isHi) "आज" else "Today"
    fun yesterday(isHi: Boolean) = if (isHi) "कल" else "Yesterday"
    fun allDates(isHi: Boolean) = if (isHi) "सभी तारीखें" else "All Dates"
    fun customDate(isHi: Boolean) = if (isHi) "तारीख चुनें..." else "Custom Date..."
    fun allStatus(isHi: Boolean) = if (isHi) "सभी स्थिति" else "All Status"
    fun received(isHi: Boolean) = if (isHi) "प्राप्त हुआ" else "Received"
    fun inProcess(isHi: Boolean) = if (isHi) "पिसाई जारी" else "In Process"
    fun ready(isHi: Boolean) = if (isHi) "तैयार है" else "Ready"
    fun delivered(isHi: Boolean) = if (isHi) "डिलीवर हो गया" else "Delivered"
    fun unpaid(isHi: Boolean) = if (isHi) "बकाया" else "Unpaid"
    fun markPaid(isHi: Boolean) = if (isHi) "भुगतान प्राप्त" else "Mark Paid"
    fun markInProcess(isHi: Boolean) = if (isHi) "पिसाई शुरू करें" else "Mark In Process"
    fun markReady(isHi: Boolean) = if (isHi) "तैयार मार्क करें" else "Mark Ready"
    fun markDelivered(isHi: Boolean) = if (isHi) "डिलीवर करें" else "Mark Delivered"
    fun pendingDue(isHi: Boolean) = if (isHi) "बकाया राशि" else "Pending Due"
    fun paidStatus(isHi: Boolean) = if (isHi) "भुगतान पूर्ण" else "PAID"

    // Profile Screen
    fun myProfile(isHi: Boolean) = if (isHi) "मेरी प्रोफ़ाइल" else "My Profile"
    fun username(isHi: Boolean) = if (isHi) "यूज़रनेम" else "Username"
    fun outletDetails(isHi: Boolean) = if (isHi) "मिल की जानकारी" else "MILL Outlet Details"
    fun managerTools(isHi: Boolean) = if (isHi) "मिल प्रबंधक टूल्स" else "MILL Manager Tools"
    fun manageGrainRates(isHi: Boolean) = if (isHi) "अनाज पिसाई दरें प्रबंधित करें" else "Manage grain services and price per KG"
    fun exportReports(isHi: Boolean) = if (isHi) "बिक्री रिपोर्ट डाउनलोड करें (CSV)" else "Export Sales Reports (CSV)"
    fun exportReportsSub(isHi: Boolean) = if (isHi) "आज, मासिक या चुनी गई तारीख की रिपोर्ट" else "Download Excel/CSV reports for today, monthly or custom range"
    fun languagePref(isHi: Boolean) = if (isHi) "भाषा चयन" else "Language / भाषा"
    fun appLanguage(isHi: Boolean) = if (isHi) "ऐप की भाषा" else "App Language"
    fun signOut(isHi: Boolean) = if (isHi) "अकाउंट से लॉगआउट करें" else "Sign Out Account"

    // Earnings
    fun totalEarning(isHi: Boolean) = if (isHi) "कुल कमाई" else "TOTAL EARNING"
    fun totalWeightGround(isHi: Boolean) = if (isHi) "कुल पिसाई वजन" else "Total Weight Ground"
    fun totalOrders(isHi: Boolean) = if (isHi) "कुल ऑर्डर्स" else "Total Orders"
    fun earningsByGrain(isHi: Boolean) = if (isHi) "अनाज अनुसार कमाई" else "Earnings by Grain / Service"
    fun thisMonth(isHi: Boolean) = if (isHi) "इस महीने" else "This Month"

    // Service Names Localization
    fun localizeService(name: String, isHi: Boolean): String {
        if (!isHi) return name
        val lower = name.trim().lowercase()
        return when {
            lower.contains("wheat") || lower.contains("गेहूं") -> "गेहूं पिसाई"
            lower.contains("daliya") || lower.contains("दलिया") -> "दलिया"
            lower.contains("oil") || lower.contains("सरसों") || lower.contains("तेल") -> "तेल पिराई"
            lower.contains("rice") || lower.contains("चावल") -> "चावल पिसाई"
            lower.contains("besan") || lower.contains("gram") || lower.contains("चना") -> "चना / बेसन पिसाई"
            lower.contains("maize") || lower.contains("corn") || lower.contains("मक्का") -> "मक्का पिसाई"
            lower.contains("spices") || lower.contains("spice") || lower.contains("मसाला") -> "मसाला पिसाई"
            lower.contains("multigrain") || lower.contains("multi-grain") -> "मल्टीग्रेन पिसाई"
            lower.contains("bajra") || lower.contains("बाजरा") -> "बाजरा पिसाई"
            lower.contains("jowar") || lower.contains("ज्वार") -> "ज्वार पिसाई"
            lower.contains("custom") || lower.contains("other") || lower.contains("अन्य") -> "अन्य सेवा"
            else -> name
        }
    }

    // Order Confirmation Modal
    fun orderConfirmedTitle(orderNum: Int, isHi: Boolean) = if (isHi) "ऑर्डर #$orderNum कन्फर्म हुआ" else "Order #$orderNum Confirmed"
    fun dailyOrderToken(orderNum: Int, isHi: Boolean) = if (isHi) "दैनिक टोकन संख्या: #$orderNum" else "Daily Order Number: #$orderNum"
    fun customerLabel(name: String, isHi: Boolean) = if (isHi) "ग्राहक: $name" else "Customer: $name"
    fun serviceSummary(service: String, weight: Double, isHi: Boolean) = if (isHi) "सेवा: $service • $weight किलो" else "Service: $service • $weight KG"
    fun totalAmountLabel(amountFormatted: String, paymentStatus: String, isHi: Boolean) = 
        if (isHi) "कुल राशि: $amountFormatted (${if (paymentStatus.equals("paid", true)) "भुगतान हो गया" else "बाकी"})" 
        else "Total Amount: $amountFormatted (${paymentStatus.uppercase()})"
    fun done(isHi: Boolean) = if (isHi) "पूर्ण" else "Done"
    fun whatsApp(isHi: Boolean) = if (isHi) "व्हाट्सएप" else "WhatsApp"
}
