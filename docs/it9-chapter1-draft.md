# LocalLift PH: A Web-Based Marketplace Platform for Local Products and Verified Sellers

## Cover Page Details

Project Title: `LocalLift PH: A Web-Based Marketplace Platform for Local Products and Verified Sellers`

Submitted To: `KIMBERLY MARIE ARIAS`

Submitted By: `[Replace with group members in ascending order by last name]`

Month and Year: `April 2026`

## CHAPTER 1

## Introduction

### Background of the Study

Micro, small, and medium enterprises (MSMEs) remain a major part of the Philippine economy, but many local sellers still depend on fragmented selling channels such as personal social media pages, chat-based ordering, and manual coordination with customers. While these channels are familiar and easy to start with, they often create problems in product discovery, price comparison, order tracking, and long-term customer trust. Buyers may find it difficult to identify legitimate sellers, compare available products, or communicate clearly about orders. Sellers, on the other hand, may struggle to present their products professionally, manage inquiries efficiently, and maintain organized records of transactions.

The continued growth of internet usage in the Philippines shows that digital platforms are already central to how people search, communicate, and transact. According to the Philippine Statistics Authority, 67.3 percent of individuals aged 10 years and over used the internet in 2024, which indicates a strong foundation for web-based systems that improve access to products and services. At the same time, the Department of Trade and Industry has repeatedly emphasized digitalization as a necessary path for MSME competitiveness, noting that hundreds of thousands of MSMEs have already been onboarded to e-commerce channels through government-supported initiatives. These developments show that local enterprises are increasingly expected to participate in more structured and technology-enabled marketplaces.

Despite this momentum, many local businesses still lack a focused platform that highlights local products while also providing better control over seller legitimacy, product presentation, and transaction flow. Existing informal selling practices often lead to inconsistent product information, delayed responses, and weak accountability. Buyers usually rely on message threads for product inquiries, order confirmation, and follow-up, which can cause misunderstandings and poor user experience. In addition, the absence of centralized shop, cart, checkout, and order management features can reduce both efficiency and consumer confidence.

To address these conditions, the researchers propose **LocalLift PH**, a web-based marketplace platform designed to connect buyers with verified local sellers through a more organized digital environment. The system provides separate workflows for buyers, sellers, and administrators. Buyers can browse products and shops, manage carts, place orders, maintain delivery addresses, exchange messages with sellers, and leave product reviews. Sellers can register, submit an application for approval, manage products, view orders, monitor earnings, respond to buyer messages, and maintain shop settings. Administrators can review seller applications and approve or reject product listings before they become visible to buyers. Through these features, the proposed system aims to support local entrepreneurship while making online buying and selling more structured, transparent, and reliable.

### Statement of the Problem

#### General Problem

Local sellers and buyers often rely on fragmented and informal online selling processes that do not provide a centralized, trustworthy, and efficient marketplace for discovering products, managing transactions, and supporting local businesses.

#### Specific Problems

1. Buyers have difficulty finding local products in one organized platform because listings are often scattered across separate pages and informal channels.
2. Buyers cannot easily evaluate seller legitimacy when there is no structured seller verification and product approval workflow.
3. Manual or chat-based selling processes make product inquiry, ordering, and follow-up slower and more prone to misunderstanding.
4. Sellers lack an integrated environment for managing products, orders, earnings, customer messages, and shop information.
5. Administrators need a more systematic way to review seller applications and moderate product listings before publication.
6. The absence of built-in cart, checkout, address management, order history, and review features reduces the efficiency and credibility of the buying process.

### Objectives

#### General Objective

To develop a web-based marketplace platform for local products that enables verified sellers to manage their stores and allows buyers to browse, communicate, and transact through a centralized and reliable system.

#### Specific Objectives

1. To provide buyer-side features for account access, shop browsing, product viewing, cart management, checkout, address management, order tracking, and product reviews.
2. To provide seller-side features for registration, application submission, store setup, product management, order monitoring, earnings viewing, messaging, and shop settings management.
3. To implement an administrator workflow for reviewing seller applications and approving or rejecting product listings.
4. To improve trust and marketplace quality by allowing only approved sellers and approved products to be visible to buyers.
5. To support more organized buyer-seller communication through an integrated messaging module with text and image sharing.
6. To create a more efficient and user-friendly digital environment that promotes local products and supports the online growth of small businesses.

### Scope and Limitations

#### Scope

The proposed system is a web-based marketplace platform focused on promoting local products through a centralized online environment. The system supports three primary user roles: buyers, sellers, and administrators.

For buyers, the system includes account access, browsing of shops and products, viewing of product categories, adding items to a cart, selecting delivery addresses, placing orders, reviewing personal order history, cancelling eligible orders, starting conversations with sellers, sending chat messages, and submitting product reviews.

For sellers, the system includes account registration, seller application submission, shop setup, product creation, product inventory and status management, viewing received orders, checking earnings summaries, managing buyer conversations, editing seller profile information, and maintaining shop settings.

For administrators, the system includes login access, seller application review, seller approval status management, and product approval or rejection before listings become available to buyers.

The system is implemented as a Laravel-based web application with a PHP backend, MySQL-compatible relational database support through Laravel migrations and models, and a frontend built using Blade templates, Tailwind CSS, and Vite-based asset management.

#### Limitations

The study is limited to a web application and does not include a dedicated mobile application for Android or iOS.

The system focuses on marketplace operations within the platform and does not currently include online payment gateway integration, automated courier tracking, advanced analytics, recommendation engines, or external logistics API integration.

Seller verification and product approval depend on administrator review, which means the quality of moderation is still influenced by human decision-making and processing time.

The platform is intended for local product discovery and marketplace interaction; therefore, broader enterprise features such as accounting, taxation automation, warehouse optimization, and multi-branch business management are outside the scope of the project.

The effectiveness of the system also depends on internet access, proper user adoption, and the availability of updated product and seller information.

### Functional Requirements

1. The system shall allow buyers, sellers, and administrators to log in using their respective access flows.
2. The system shall allow new seller accounts to register and submit application requirements for approval.
3. The system shall allow administrators to approve or reject seller applications.
4. The system shall allow sellers to add, edit, and manage product listings with details such as name, category, description, price, stock, dimensions, shipping fee, image, and status.
5. The system shall allow administrators to approve or reject pending product listings.
6. The system shall display only approved, active products from approved sellers to buyer-facing pages.
7. The system shall allow buyers to browse products, categories, and seller shops.
8. The system shall allow buyers to view product details and seller shop information.
9. The system shall allow buyers to add products to a cart and update or remove cart items.
10. The system shall prevent users from ordering their own products.
11. The system shall allow buyers to select saved addresses and proceed to checkout.
12. The system shall compute subtotal, shipping fee, and total order cost during checkout.
13. The system shall allow buyers to place orders and store the order with itemized product details.
14. The system shall allow buyers to view their order history and order details.
15. The system shall allow buyers to cancel eligible orders and record cancellation reasons.
16. The system shall allow sellers to view order information related to their listed products.
17. The system shall allow sellers to view summary information such as sales, active products, pending orders, and conversation counts.
18. The system shall allow buyers and sellers to start conversations and exchange messages within the platform.
19. The system shall allow chat messages to include text and image attachments.
20. The system shall allow users to manage profile information and seller shop settings.
21. The system shall allow buyers to submit product reviews after purchase-related interaction.

## Suggested References

Use these in your background section and format them according to your required citation style:

1. Philippine Statistics Authority. (2025, August 14). *Percentage of Households with Internet Connection Increased to 48.8 percent in 2024; Two in Every Three Individuals Aged 10 Years and Over Used the Internet*. https://psa.gov.ph/content/percentage-households-internet-connection-increased-488-percent-2024-two-every-three
2. Department of Trade and Industry. (2024, July 13). *Go Digital: DTI Secretary Pascual rallies MSMEs to transform and thrive*. https://www.dti.gov.ph/uncategorized/dti-go-digital-dti-secretary-pascual-rallies-msmes-transform-thrive
3. Department of Trade and Industry. (2024, October 17). *DTI Cebu rolls out MSME Digitalization Caravan 2.0: Empowering entrepreneurs through digital payments*. https://www.dti.gov.ph/dti-regions/dti-region-7/dti-region-7-news/dti-dti-cebu-rolls-out-msme-digitalization-caravan-2-0-empowering-entrepreneurs-digital-payments

## Notes for Final Editing

1. Replace the submitted-by line with your actual group members.
2. If your instructor wants stricter wording, change "web-based marketplace platform" to whatever exact title your group approved.
3. If your class requires Chapter 1 to be in paragraph form only, the specific problems, objectives, scope, and functional requirements can be converted from bullets into paragraphs.
