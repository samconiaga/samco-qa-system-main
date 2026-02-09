import { Link, usePage } from "@inertiajs/react";
import { Icon } from "@iconify/react"; // jangan lupa icon
import React, { useEffect } from "react";
import { route } from "ziggy-js";
import { useTranslation } from "react-i18next";

export default function Menu({ ...props }) {
    const isActive = (namedRoute) => route().current(namedRoute) ? "active-page" : "";
    const isDropDownActive = (namedRoute) => route().current(namedRoute) ? "open" : "";
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const role = auth.user.roles.map((role) => role.name);
    const permissions = auth.user.permissions.map((permission) => permission.name);
    
    const { url } = usePage();
    useEffect(() => {
        const openActiveDropdown = () => {
            const allDropdowns = document.querySelectorAll(".sidebar-menu .dropdown");
            allDropdowns.forEach((dropdown) => {
                const submenuLinks = dropdown.querySelectorAll("a[data-route]");
                submenuLinks.forEach((link) => {
                    const namedRoute = link.dataset.route;
                    if (namedRoute && route().current(namedRoute)) {
                        dropdown.classList.add("open");
                        const submenu = dropdown.querySelector(".sidebar-submenu");
                        if (submenu) {
                            submenu.style.maxHeight = `${submenu.scrollHeight}px`;
                        }
                    }
                });
            });
        };

        openActiveDropdown();
    }, [url]);
    return (
        <ul className='sidebar-menu' id='sidebar-menu'>

            <li className="mb-16">
                <Link href={route('dashboard')} className={isActive('dashboard')}>
                    <Icon icon='mdi:home' className='menu-icon' />
                    <span>{t('dashboard')}</span>
                </Link>
            </li>
            {(role.includes('Administrator') || (role.includes('Employee') && permissions.includes('Create Master Data'))) && (
                <>
                    <li className='sidebar-menu-group-title'>{t('master_data')}</li>
                    <li>
                        <Link href={route('master-data.departments.index')} className={isActive('master-data.departments.*')}>
                            <Icon icon='mdi:building' className='menu-icon' />
                            <span>{t('departments')}</span>
                        </Link>
                    </li>
                    <li>
                        <Link href={route('master-data.employees.index')} className={isActive('master-data.employees.*')}>
                            <Icon icon='mdi:user' className='menu-icon' />
                            <span>{t('employees')}</span>
                        </Link>
                    </li>
                    <li>
                        <Link href={route('master-data.positions.index')} className={isActive('master-data.positions.*')}>
                            <Icon icon='mdi:account-tie' className='menu-icon' />
                            <span>{t('positions')}</span>
                        </Link>
                    </li>
                    <li>
                        <Link href={route('master-data.products.index')} className={isActive('master-data.products.*')}>
                            <Icon icon='mdi:pill' className='menu-icon' />
                            <span>{t('products')}</span>
                        </Link>
                    </li>
                    <li>
                        <Link href={route('master-data.capa-types.index')} className={isActive('master-data.capa-types.*')}>
                            <Icon icon='mdi:database-outline' className='menu-icon' />
                            <span>{t('capa_types')}</span>
                        </Link>
                    </li>
                    <li className={`dropdown ${isDropDownActive('master-data.attribute-types.*')}`}>
                        <Link href="#">
                            <Icon
                                icon="mdi:widgets"
                                className="menu-icon"
                            />
                            <span>{t('attribute_types')}</span>
                        </Link>
                        <ul className="sidebar-submenu">

                            <li>
                                <Link
                                    href={route('master-data.attribute-types.scope-of-changes.index')}
                                    data-route="master-data.attribute-types.scope-of-changes.index"
                                    className={isActive('master-data.attribute-types.scope-of-changes.*')}
                                >
                                    <i className={`ri-circle-fill circle-icon w-auto ${isActive('master-data.attribute-types.scope-of-changes.*') ? 'text-danger' : ''}`} />
                                    <span>{t('scope_of_changes')}</span>
                                </Link>
                            </li>

                        </ul>
                    </li>


                </>
            )
            }
            {(role.includes('Administrator') || role.includes('Employee')) && (
                <>
                    <li className='sidebar-menu-group-title'>{t('module')}</li>
                    <li>
                        <Link href={route('change-requests.index')} className={isActive('change-requests.*')}>
                            <Icon icon='mdi:exchange' className='menu-icon' />
                            <span>{t('change_requests')}</span>
                        </Link>
                    </li>
                    <li>
                        <Link href={route('capa.issues.index')} className={isActive('capa.*')}>
                            <Icon icon='mdi:tools' className='menu-icon' />
                            <span>{t('capa')}</span>
                        </Link>
                    </li>
                    <li className='sidebar-menu-group-title'>{t('other')}</li>
                    <li>
                        <Link href={route('change-requests.index')} className={isActive('report.*')}>
                            <Icon icon='mdi:file-document-box-multiple' className='menu-icon' />
                            <span>{t('report')}</span>
                        </Link>
                    </li>
                </>
            )}
        </ul >
    );
}
