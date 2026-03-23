<aside class="">
    <div class="side_header">
        <img src="{{ asset('build/assets/images/favicon.png') }}" class="thumb">
        <a href="#" class="sidebar_toggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 1.25H9C8.7 1.25 8.406 1.26 8.121 1.274C8.08134 1.26294 8.04088 1.25491 8 1.25C7.91049 1.25156 7.82203 1.26952 7.739 1.303C3.213 1.655 1.25 4.011 1.25 9V15C1.25 19.989 3.213 22.345 7.739 22.7C7.82222 22.7325 7.91068 22.7494 8 22.75C8.04088 22.7451 8.08134 22.7371 8.121 22.726C8.406 22.74 8.696 22.75 9 22.75H15C20.432 22.75 22.75 20.433 22.75 15V9C22.75 3.567 20.432 1.25 15 1.25ZM7.25 21.15C3.968 20.736 2.75 18.975 2.75 15.002V9C2.75 5.027 3.968 3.266 7.25 2.852V21.15ZM21.25 15C21.25 19.614 19.614 21.25 15 21.25H9C8.913 21.25 8.835 21.25 8.75 21.244V2.756C8.835 2.756 8.913 2.75 9 2.75H15C19.614 2.75 21.25 4.386 21.25 9V15ZM15.53 9.971L13.5 12L15.529 14.029C15.6007 14.0982 15.6578 14.1809 15.6972 14.2724C15.7365 14.3639 15.7573 14.4623 15.7582 14.5618C15.7591 14.6614 15.7402 14.7602 15.7025 14.8524C15.6648 14.9446 15.6092 15.0283 15.5388 15.0988C15.4684 15.1692 15.3847 15.225 15.2925 15.2627C15.2004 15.3005 15.1016 15.3195 15.0021 15.3187C14.9025 15.3179 14.804 15.2972 14.7125 15.258C14.621 15.2187 14.5382 15.1616 14.469 15.09L11.909 12.53C11.8393 12.4604 11.7841 12.3778 11.7464 12.2869C11.7087 12.1959 11.6892 12.0984 11.6892 12C11.6892 11.9015 11.7087 11.8041 11.7464 11.7131C11.7841 11.6222 11.8393 11.5396 11.909 11.47L14.469 8.91C14.6105 8.77345 14.8 8.69794 14.9967 8.69974C15.1933 8.70154 15.3814 8.78051 15.5204 8.91963C15.6594 9.05875 15.7381 9.2469 15.7398 9.44355C15.7414 9.6402 15.6667 9.82961 15.53 9.971Z" fill="black"/>
            </svg>                            
        </a>
    </div>

    <div class="side_content">
        <div class="menu_title">Main</div>
        <ul class="side_menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="menu_toggle">
                    <figure>
                        <!-- Dashboard Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                            <path d="M3 9L12 2L21 9V20A2 2 0 0 1 19 22H5A2 2 0 0 1 3 20V9Z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </figure>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.clients.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Clients Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                            <path d="M17 21V19A4 4 0 0 0 13 15H5A4 4 0 0 0 1 19V21"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21V19A4 4 0 0 0 20 15"></path>
                            <path d="M16 3.13A4 4 0 0 1 20 7"></path>
                        </svg>
                    </figure>
                    <span>Clients Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.clients.index') }}">All Clients</a></li>
                    <li><a href="{{ route('admin.clients.create') }}">Add Client</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.staff.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Staff Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                            <path d="M20 21V19A4 4 0 0 0 16 15H8A4 4 0 0 0 4 19V21"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </figure>
                    <span>Staff Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.staff.index') }}">All Staffs</a></li>
                    <li><a href="{{ route('admin.staff.create') }}">Add Staff</a></li>
                    <li><a href="{{ route('admin.departments.index') }}">Departments</a></li>
                    <li><a href="{{ route('admin.employee-types.index') }}">Employee Type</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.projects.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Projects Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 3H8V7H16V3Z"></path>
                        </svg>
                    </figure>
                    <span>Project Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.projects.index') }}">All Projects</a></li>
                    <li><a href="{{ route('admin.projects.create') }}">Add New Project</a></li>
                    <li><a href="{{ route('admin.project-statuses.index') }}">Project Status</a></li>
                    <li><a href="{{ route('admin.project-types.index') }}">Project Type</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.vendors.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Vendors Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </figure>
                    <span>Vendor Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.vendors.index') }}">All Vendors</a></li>
                    <li><a href="{{ route('admin.vendors.create') }}">Add New Vendor</a></li>
                    <li><a href="{{ route('admin.vendor-category.index') }}">Vendor Category</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.leads.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Leads Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone">
                            <path d="M22 16.92V21A2 2 0 0 1 20 23C10.06 23 1 13.94 1 4A2 2 0 0 1 3 2H7.09A2 2 0 0 1 9 3.5L11.59 7.91A2 2 0 0 1 11 10.5L9.5 12A16 16 0 0 0 20 22.5L21.5 21A2 2 0 0 1 22 19.92Z"></path>
                        </svg>
                    </figure>
                    <span>Leads Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.leads.index') }}">All Leads</a></li>
                    <li><a href="{{ route('admin.leads.create') }}">Add New Lead</a></li>
                    <li><a href="{{ route('admin.lead-sources.index') }}">Lead Source</a></li>
                    <li><a href="{{ route('admin.lead-statuses.index') }}">Lead Status</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.tasks.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Task Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.tasks.index') }}">All Tasks</a></li>
                    <li><a href="{{ route('admin.tasks.create') }}">Add New Task</a></li>
                    <li><a href="{{ route('admin.task-priorities.index') }}">Task Priority</a></li>
                    <li><a href="{{ route('admin.task-statuses.index') }}">Task Status</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.inventory-items.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Inventory Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.inventory-items.index') }}">All Items</a></li>
                    <li><a href="{{ route('admin.inventory-items.create') }}">Add New Item</a></li>
                    <li><a href="{{ route('admin.inventory-categories.index') }}">Inventory Categories</a></li>
                    <li><a href="{{ route('admin.inventory-units.index') }}">Inventory Units</a></li>
                    <li><a href="{{ route('admin.inventory-brands.index') }}">Inventory Brands</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.invoices.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Invoice Management</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.invoices.index') }}">All Invoices</a></li>
                    <li><a href="{{ route('admin.invoices.create') }}">Add New Invoice</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.purchase-orders.index') }}">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Create PO</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.purchase-receipts.index') }}">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Create GRN</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.expenses.index') }}" class="menu_toggle">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Expenses</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.expenses.index') }}">All Expenses</a></li>
                    <li><a href="{{ route('admin.expense-categories.index') }}">Expense Categories</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="menu_toggle">
                    <figure>
                        <!-- Tasks Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </figure>
                    <span>Reports</span>
                    <i>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a2a2a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </i>
                </a>
                <ul class="sub_menu">
                    <li class="parent_menu"><a href="{{ route('admin.reports.gstr1') }}">GSTR-1 Report</a></li>
                    <li><a href="{{ route('admin.reports.financial.summary') }}">Financial Summary</a></li>
                    <li><a href="{{ route('admin.reports.monthly_profit') }}">Monthly Profit</a></li>
                    <li><a href="{{ route('admin.reports.expense_category') }}">Expense By Category</a></li>
                    <li><a href="{{ route('admin.reports.tax_summary') }}">Tax Summary</a></li>
                    <li><a href="{{ route('admin.reports.outstanding') }}">Outstanding</a></li>
                    <li><a href="{{ route('admin.reports.cashflow') }}">Cashflow</a></li>
                    <li><a href="{{ route('admin.reports.balance.sheet') }}">Balance Sheet</a></li>
                    <li><a href="{{ route('admin.reports.profit.loss') }}">Profit Loss</a></li>
                    <li><a href="{{ route('admin.reports.revenue.chart') }}">Revenue Chart</a></li>
                    <li><a href="{{ route('admin.reports.profit.product') }}">Profit By Product</a></li>
                    <li><a href="{{ route('admin.reports.profit.client') }}">Profit By Client</a></li>
                    <li><a href="{{ route('admin.reports.gst.wise') }}">GST Wise Revenue</a></li>
                    <li><a href="{{ route('admin.reports.year_comparison') }}">Year Comparison</a></li>
                    <li><a href="{{ route('admin.reports.inventory.valuation') }}">Inventory Valuation</a></li>
                    <li><a href="{{ route('admin.reports.aging_report') }}">Aging Report</a></li>
                    <li><a href="{{ route('admin.reports.stock.aging') }}">Stock Aging</a></li>
                    <li><a href="{{ route('admin.reports.low.stock') }}">Low Stock</a></li>
                    <li><a href="{{ route('admin.reports.daily.closing') }}">Daily Closing</a></li>
                    <li><a href="{{ route('admin.reports.dead.stock') }}">Dead Stock</a></li>
                </ul>
            </li>            
        </ul>
    </div>
    <div class="side_footer">
        <a href="{{ route('admin.settings.index') }}" class="profile_btn">
            <figure>
                <img src="https://ui-avatars.com/api/?background=ffffff&color=000" />
            </figure>
            <figcaption>
                <h5>Jhon Doe</h5>
                <h6>jhon.doe@gmail.com</h6>
            </figcaption>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
    </div>
</aside>