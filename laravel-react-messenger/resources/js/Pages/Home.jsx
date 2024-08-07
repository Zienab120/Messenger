import ChatLayout from "@/Layouts/ChatLayout.jsx";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";

export default function Home({ auth }) {
    return <>Messages</>;
    // return (
    //     <AuthenticatedLayout user={page.props.auth.user}>
    //         <ChatLayout children={page}/>
    //     </AuthenticatedLayout>
    // );
}

Home.Layout = (page)=> {
    return (
        <AuthenticatedLayout user={page.props.auth.user}>
            <ChatLayout children={page}/>
        </AuthenticatedLayout>
    );
}
 // Home;
