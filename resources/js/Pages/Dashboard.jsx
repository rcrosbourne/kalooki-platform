import React, {useEffect, useState} from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/inertia-react';

export default function Dashboard(props) {
    const [userLoggedIn, setUserLoggedIn] = useState(null);
    useEffect(() => {
        window.Echo.channel(`user-logged-in`)
            .listen('UserLoggedIn', (e) => {
                setUserLoggedIn(e);
            });
        return () => {
            window.Echo.leaveChannel(`user-logged-in`);
        }
    }, []);
    return (
        <AuthenticatedLayout
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard"/>

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">{props.auth.user.name} You're logged in!</div>

                        {userLoggedIn && <div className="p-6 bg-white border-b border-gray-200">{userLoggedIn.user.name} Just logged In</div>}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
