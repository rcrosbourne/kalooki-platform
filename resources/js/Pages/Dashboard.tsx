import React, {useEffect, useState} from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from "@inertiajs/inertia-react";
import {showNotification} from '@mantine/notifications';
import {IconX} from "@tabler/icons";
import route from '../../../vendor/tightenco/ziggy/dist/index.m';

export default function Dashboard(props) {
    const [userLoggedIn, setUserLoggedIn] = useState(null);
    useEffect(() => {
        window.Echo.channel(`user-logged-in`)
            .listen('UserLoggedIn', (e) => {
                showNotification({
                    title: 'User logged in',
                    message: e.user.name + ' logged in',
                    autoClose: 3000,
                    icon: <IconX />,
                })
                setUserLoggedIn(e);
            });
        return () => {
            window.Echo.leaveChannel(`user-logged-in`);
        }
    }, []);
    return (
        <AuthenticatedLayout
            auth={props.auth}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard"/>

            <div className="py-12 bg-dark-blue min-h-screen">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-gray-600 border-b border-gray-700 text-light-green">
                            <Link href={route('game.create')} method="post" as="button" className="">
                                Create a Game
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
