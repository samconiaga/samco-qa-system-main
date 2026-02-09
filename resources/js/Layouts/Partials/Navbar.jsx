import { Icon } from "@iconify/react";
import { Link, useForm, usePage } from "@inertiajs/react";
import ThemeToggleButton from "../../src/helper/ThemeToggleButton";
import { useTranslation } from "react-i18next";
import { confirmAlert } from "../../src/components/ui/SweetAlert";
import CheckBoxInput from "../../src/components/ui/CheckBoxInput";
import { useState, useEffect, useRef } from "react";
import i18n from "../../src/i18n";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";
import { createPortal } from "react-dom";

export default function Navbar({ sidebarControl, mobileMenuControl, sidebarActive }) {
    const { t } = useTranslation();
    const { post } = useForm();
    const user = usePage().props.auth.user;

    const [currentLang, setCurrentLang] = useState(i18n.language);
    const [openProfile, setOpenProfile] = useState(false);
    const profileRef = useRef(null);

    /* ================= LOGOUT ================= */
    const handleLogout = () => {
        confirmAlert(
            t("are_you_sure"),
            t("logout_confirmation"),
            "warning",
            () => post(route("logout"))
        );
    };

    /* ================= LANGUAGE ================= */
    const handleChangeLanguage = (e) => {
        const newLang = e.target.value;
        setCurrentLang(newLang);
        i18n.changeLanguage(newLang);

        axios.post("/set-language", { locale: newLang })
            .then(() => Inertia.reload())
            .catch(err => console.error(err));
    };

    /* ================= CLICK OUTSIDE ================= */
    useEffect(() => {
        const handleClick = (e) => {
            if (profileRef.current && !profileRef.current.contains(e.target)) {
                setOpenProfile(false);
            }
        };

        if (openProfile) {
            document.addEventListener("mousedown", handleClick);
        }

        return () => document.removeEventListener("mousedown", handleClick);
    }, [openProfile]);

    useEffect(() => {
        const handleScroll = () => {
            setOpenProfile(false);
        };

        if (openProfile) {
            window.addEventListener("scroll", handleScroll, true);
        }

        return () => {
            window.removeEventListener("scroll", handleScroll, true);
        };
    }, [openProfile]);

    const isDark = document.body.classList.contains('dark');
    return (
        <>
            <div className="navbar-header">
                <div className="row align-items-center justify-content-between">
                    {/* LEFT */}
                    <div className="col-auto">
                        <div className="d-flex align-items-center gap-4">
                            <button
                                type="button"
                                className="sidebar-toggle"
                                onClick={sidebarControl}
                            >
                                {sidebarActive ? (
                                    <Icon icon="iconoir:arrow-right" className="icon text-2xl" />
                                ) : (
                                    <Icon icon="heroicons:bars-3-solid" className="icon text-2xl" />
                                )}
                            </button>

                            <button
                                type="button"
                                className="sidebar-mobile-toggle"
                                onClick={mobileMenuControl}
                            >
                                <Icon icon="heroicons:bars-3-solid" className="icon" />
                            </button>
                        </div>
                    </div>

                    {/* RIGHT */}
                    <div className="col-auto">
                        <div className="d-flex align-items-center gap-3">
                            <ThemeToggleButton />

                            {/* ================= LANGUAGE (BOOTSTRAP OK) ================= */}
                            <div className="dropdown d-none d-sm-inline-block">
                                <button
                                    className="w-40-px h-40-px rounded-circle"
                                    data-bs-toggle="dropdown"
                                >
                                    <img
                                        src={`/assets/images/flags/${currentLang === "id-ID" ? "id" : "gb"}.png`}
                                        className="w-100 h-100 rounded-circle"
                                    />
                                </button>

                                <div className='dropdown-menu to-top dropdown-menu-sm'>
                                    <div className='py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2'>
                                        <div>
                                            <h6 className='text-lg text-primary-light fw-semibold mb-0'>
                                                {t('Choose Language')}
                                            </h6>
                                        </div>
                                    </div>
                                    <div className='max-h-400-px overflow-y-auto scroll-sm pe-8'>
                                        <div className='form-check style-check d-flex align-items-center justify-content-between mb-16'>
                                            <label
                                                className='form-check-label line-height-1 fw-medium text-secondary-light'
                                                htmlFor='indonesia'
                                            >
                                                <span className='text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3'>
                                                    <img
                                                        src='/assets/images/flags/id.png'
                                                        alt=''
                                                        className='w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0'
                                                    />
                                                    <span className='text-md fw-semibold mb-0'>
                                                        {t('Indonesian')}
                                                    </span>
                                                </span>
                                            </label>
                                            <CheckBoxInput
                                                className='form-check-input'
                                                type='radio'
                                                name='language'
                                                id='indonesia'
                                                value='id-ID'
                                                checked={currentLang === 'id-ID'}
                                                onChange={handleChangeLanguage}
                                            />
                                        </div>
                                        <div className='form-check style-check d-flex align-items-center justify-content-between mb-16'>
                                            <label
                                                className='form-check-label line-height-1 fw-medium text-secondary-light'
                                                htmlFor='english'
                                            >
                                                <span className='text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3'>
                                                    <img
                                                        src='/assets/images/flags/gb.png'
                                                        alt=''
                                                        className='w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0'
                                                    />
                                                    <span className='text-md fw-semibold mb-0'>
                                                        {t('English')}
                                                    </span>
                                                </span>
                                            </label>
                                            <CheckBoxInput
                                                className='form-check-input'
                                                type='radio'
                                                value='en-GB'
                                                checked={currentLang === 'en-GB'}
                                                onChange={handleChangeLanguage}
                                                name='language'
                                                id='english'
                                            />
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {/* ================= PROFILE (PORTAL) ================= */}
                            <button
                                type="button"
                                className="rounded-circle border-0 bg-transparent"
                                onClick={() => setOpenProfile(prev => !prev)}
                            >
                                <img
                                    src={user?.photo}
                                    alt="user"
                                    className="w-40-px h-40-px rounded-circle"
                                />
                            </button>

                            {openProfile &&
                                createPortal(
                                    <div className="profile-portal" >
                                        <div className="profile-card" ref={profileRef}
                                            style={{
                                                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                                color: isDark ? '#f9fafb' : '#111827'
                                            }}>
                                            <div className="profile-header">
                                                <div>
                                                    <h6 className="mb-1" style={{ color: isDark ? '#f9fafb' : '#111827' }}>{user.name}</h6>
                                                    <small style={{ color: isDark ? '#f9fafb' : '#111827' }}>{user.email}</small>
                                                </div>
                                                <button
                                                    type="button"
                                                    className="border-0 bg-transparent"
                                                    onClick={() => setOpenProfile(false)}
                                                >
                                                    <Icon icon="radix-icons:cross-1" />
                                                </button>
                                            </div>

                                            <ul className="profile-menu">
                                                <li>
                                                    <Link href={route('profile.index')}>
                                                        <Icon icon="solar:user-linear" />
                                                        {t('Profile')}
                                                    </Link>
                                                </li>
                                                <li>
                                                    <button onClick={handleLogout}>
                                                        <Icon icon="lucide:power" />
                                                        {t("logout")}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>,
                                    document.body
                                )}

                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
