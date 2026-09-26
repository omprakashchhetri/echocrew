<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Single source of truth for EchoCrew SEO data.
 * Nothing here may be invented. Empty values stay empty until the business
 * supplies real information (see $nap below).
 */
class Seo extends BaseConfig
{
    public string $brand    = 'EchoCrew';
    public string $tagline  = 'Custom Software, CRM & Digital Solutions';
    public string $locale   = 'en_IN';

    /**
     * Name, Address, Phone. REQUIRED for local ranking in Siliguri.
     * Fill these with the real, verifiable business details, matching the
     * Google Business Profile character for character. Leave blank rather
     * than guessing — wrong NAP data actively suppresses local rankings.
     */
    public array $nap = [
        'legalName'    => 'EchoCrew',
        'street'       => '',            // e.g. 'Sevoke Road'
        'locality'     => 'Siliguri',
        'region'       => 'West Bengal',
        'postalCode'   => '',            // e.g. '734001'
        'country'      => 'IN',
        'telephone'    => '',            // e.g. '+91XXXXXXXXXX'
        'email'        => '',
        'latitude'     => '',            // e.g. '26.7271'
        'longitude'    => '',            // e.g. '88.3953'
        'googleMapUrl' => '',            // Google Business Profile share link
    ];

    /** Real social/profile URLs only. Used for sameAs entity confirmation. */
    public array $profiles = [
        // 'https://www.linkedin.com/company/...',
        // 'https://github.com/omprakashchhetri',
        // 'https://www.facebook.com/...',
    ];

    /** Areas served — drives local relevance without keyword stuffing. */
    public array $areasServed = [
        'Siliguri', 'Jalpaiguri', 'Darjeeling', 'Kalimpong',
        'Cooch Behar', 'North Bengal', 'Sikkim', 'India',
    ];

    public array $openingHours = [
        'days'  => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        'opens' => '10:00',
        'closes' => '19:00',
    ];

    /**
     * Service landing pages. Each becomes /services/{slug} with its own
     * title, description, H1 and Service schema. Separate URLs are what
     * actually rank for separate queries — the homepage cannot rank for
     * everything at once.
     */

    /** Homepage FAQ — rendered as markup AND emitted as FAQPage schema. */
    public array $homeFaq = [
        ['Do you only build websites?', 'No. EchoCrew develops complete digital systems including CRM platforms, management software, e-commerce, integrations and automation. A website is often one part of the system rather than the whole of it.'],
        ['Can you customise existing software?', 'Yes. Customising software you already own is frequently faster and cheaper than replacing it, and we will say so when that is the case.'],
        ['Can you work with legacy PHP applications?', 'Yes. Older PHP and CodeIgniter applications are familiar ground. We can extend, integrate, secure and modernise them without a full rewrite.'],
        ['Can you build a CRM from scratch?', 'Yes, when an off-the-shelf CRM cannot be shaped to your pipeline. We build around your stages, roles and reporting rather than a generic sales model.'],
        ['Can you integrate APIs?', 'Yes: payment gateways, messaging platforms, third-party services and internal applications, in both directions, with webhooks and scheduled syncs where they fit.'],
        ['Can you automate our existing workflow?', 'Yes. We map the process first, identify the steps that repeat without judgement, and automate those while keeping people at the decision points.'],
        ['Do you provide maintenance?', 'Ongoing maintenance and continued development are available and scoped per project, based on what the system needs once it is live.'],
        ['Can you integrate AI?', 'Yes, where it provides a practical business benefit. If a simple rule does the job more reliably, we will recommend the rule.'],
        ['Where is EchoCrew based?', 'EchoCrew works with businesses in Siliguri and across North Bengal, and delivers projects remotely for clients elsewhere in India and abroad.'],
    ];

    public array $services = [
        'custom-software-development' => [
            'name'  => 'Custom Software Development',
            'h1'    => 'Custom software development in Siliguri',
            'title' => 'Custom Software Development Company in Siliguri | EchoCrew',
            'desc'  => 'EchoCrew builds custom software for businesses in Siliguri and across North Bengal: web applications, internal systems and business tools built around your workflow.',
            'lead'  => 'Software built for one business: yours. We start from the process you already run and build the system around it, instead of asking your team to work the way a product wants them to.',
            'points' => [
                'Custom web applications and internal tools',
                'Portals, dashboards and client areas',
                'Role-based access and reporting',
                'Built on PHP, Laravel, CodeIgniter, Vue and MySQL',
                'Documented handover, maintainable code',
            ],
            'faq' => [
                ['How long does custom software take to build?', 'It depends on scope. A focused internal tool is measured in weeks; a full management platform in months. We scope in stages so you see working software early rather than at the end.'],
                ['Do we own the code?', 'Yes. The source code and database belong to your business.'],
            ],
        ],
        'web-development' => [
            'name'  => 'Website Development',
            'h1'    => 'Website development in Siliguri',
            'title' => 'Website Development Company in Siliguri | EchoCrew',
            'desc'  => 'Custom website development in Siliguri: fast, SEO-ready business websites, landing pages and web applications built by EchoCrew, not assembled from a theme.',
            'lead'  => 'A website should do operational work, not just sit there looking presentable. We build fast, search-ready sites and turn them into applications when the business needs one.',
            'points' => [
                'Corporate and business websites',
                'Landing pages built to convert',
                'Custom functionality instead of plugin stacks',
                'Core Web Vitals and technical SEO from the first line',
                'Responsive down to small phones',
            ],
            'faq' => [
                ['Do you build on WordPress or custom code?', 'Both. WordPress when the site is content-led and the team wants to edit it themselves; custom code when the site needs to behave like an application.'],
            ],
        ],
        'crm-development' => [
            'name'  => 'CRM Development',
            'h1'    => 'CRM development and customisation',
            'title' => 'Custom CRM Development Company in Siliguri | EchoCrew',
            'desc'  => 'Custom CRM development and CRM customisation by EchoCrew: sales pipelines, lead management, reporting and automation shaped around how your team actually sells.',
            'lead'  => 'If your team keeps a spreadsheet next to the CRM, the CRM is wrong. We reshape the one you own or build one that follows your real pipeline.',
            'points' => [
                'Lead capture from every channel',
                'Pipelines, stages and handoffs that match your process',
                'Customer history and communication logs',
                'Reporting and dashboards',
                'Workflow automation and integrations',
            ],
            'faq' => [
                ['Can you customise our existing CRM instead?', 'Usually yes, and it is often cheaper. We will tell you honestly when customising costs more than replacing.'],
            ],
        ],
        'school-management-software' => [
            'name'  => 'School Management Software',
            'h1'    => 'School management software',
            'title' => 'School Management Software Development in Siliguri | EchoCrew',
            'desc'  => 'Custom school management software built by EchoCrew: student records, staff, administration and reporting designed around how your institution actually runs.',
            'lead'  => 'Education administration built around the term, not around a generic product roadmap.',
            'points' => [
                'Student and staff records',
                'Administration and operational routines',
                'Role-based access for staff and management',
                'Reporting and data export',
                'Built and extended in stages',
            ],
            'faq' => [
                ['Can it work alongside our current system?', 'Yes. We can integrate with existing systems through APIs or migrate data in stages rather than switching everything at once.'],
            ],
        ],
        'ecommerce-development' => [
            'name'  => 'E-Commerce Development',
            'h1'    => 'E-commerce development',
            'title' => 'E-Commerce Website Development in Siliguri | EchoCrew',
            'desc'  => 'Custom e-commerce development by EchoCrew: catalogue, orders, payments, inventory and automation built around your fulfilment and your margins.',
            'lead'  => 'Commerce systems built end to end: catalogue, checkout, fulfilment and the reporting that tells you what is actually working.',
            'points' => [
                'Products, variants and inventory',
                'Orders, customers and payments',
                'Payment gateway integration',
                'Reporting and automation',
                'Built for search visibility from the start',
            ],
            'faq' => [],
        ],
        'api-integration' => [
            'name'  => 'API & System Integration',
            'h1'    => 'API and system integration',
            'title' => 'API Integration Services in Siliguri | EchoCrew',
            'desc'  => 'API integration services by EchoCrew: connect your CRM, website, payment gateway, messaging platform and internal databases so data is entered once.',
            'lead'  => 'Most businesses do not need more software. They need the software they already pay for to stop ignoring each other.',
            'points' => [
                'CRM, website and internal application integration',
                'Payment gateway and messaging platform APIs',
                'Webhooks and scheduled synchronisation',
                'Two-way data flow with clean contracts',
                'Third-party and partner APIs',
            ],
            'faq' => [],
        ],
        'business-automation' => [
            'name'  => 'Workflow Automation',
            'h1'    => 'Business workflow automation',
            'title' => 'Business Process Automation Services in Siliguri | EchoCrew',
            'desc'  => 'Workflow automation by EchoCrew: automate lead routing, notifications, data synchronisation, reporting and customer communication across your systems.',
            'lead'  => 'Anything a person repeats the same way every day is a candidate for a workflow.',
            'points' => [
                'Automatic lead creation and routing',
                'Notifications, alerts and escalations',
                'Data synchronisation between systems',
                'Scheduled and triggered reports',
                'Customer communication sequences',
            ],
            'faq' => [],
        ],
        'ai-solutions' => [
            'name'  => 'AI Solutions',
            'h1'    => 'AI solutions for business workflows',
            'title' => 'AI Solutions & AI Integration Services | EchoCrew',
            'desc'  => 'Practical AI integration by EchoCrew: support assistants, lead qualification, document processing and data analysis added where they measurably improve a workflow.',
            'lead'  => 'AI belongs inside a workflow it improves. Where a simple rule is cheaper and more reliable, we recommend the rule.',
            'points' => [
                'Support and internal assistants',
                'Lead qualification and routing',
                'Document and content processing',
                'Data analysis and summarisation',
                'AI inside existing automation chains',
            ],
            'faq' => [],
        ],
        'legacy-system-modernization' => [
            'name'  => 'Legacy System Modernisation',
            'h1'    => 'Legacy system modernisation',
            'title' => 'Legacy Software Modernisation & PHP Application Support | EchoCrew',
            'desc'  => 'EchoCrew modernises legacy PHP, CodeIgniter and CRM applications: API layers, modern interfaces, automation and security hardening without a risky full rewrite.',
            'lead'  => 'Years of business logic live inside older applications. Replacing everything is often expensive, risky and unnecessary.',
            'points' => [
                'Customisation of existing PHP and CRM applications',
                'API layers over older systems',
                'Modern interfaces on proven backends',
                'Incremental migration instead of rewrites',
                'Performance and security hardening',
            ],
            'faq' => [
                ['Our developer left and nobody understands the code. Can you help?', 'Yes. We audit the application first, document how it works, then recommend what to keep, what to replace and in what order.'],
            ],
        ],
        'digital-marketing-seo' => [
            'name'  => 'Digital Marketing & SEO',
            'h1'    => 'Digital marketing and SEO',
            'title' => 'SEO & Digital Marketing Services in Siliguri | EchoCrew',
            'desc'  => 'SEO and digital marketing by EchoCrew: technical SEO, local SEO, landing pages, analytics and conversion optimisation, run by the team that builds the site.',
            'lead'  => 'Development and growth work better when the same team owns site speed, structure and what converts.',
            'points' => [
                'Technical SEO and Core Web Vitals',
                'Local SEO and Google Business Profile',
                'Landing pages and conversion optimisation',
                'Analytics and tracking setup',
                'Content strategy and digital campaigns',
            ],
            'faq' => [],
        ],
    ];
}
