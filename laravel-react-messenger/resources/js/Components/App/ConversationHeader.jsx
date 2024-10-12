const { ArrowLeftIcon } = require("@heroicons/react/24/solid")
const { Link } = require("@inertiajs/react")

const ConversatoinHeader = ({selectedConversation})=>{
    return(
        <>
            {selectedConversation &&(
                <div className="p-3 flex justify-between items-center border-b border-slated-700">
                    <div className="flex items-center gap-3">
                        <Link href={route("dashboard")}
                        className="inline-bock sm:hidden"
                        >
                            <ArrowLeftIcon className="w-6"/>
                        </Link>
                        {selectedConversation.is_user &&(
                            <UserAvater user={selectedConversation}/>
                        )}
                        <div>
                            <h3>{selectedConversation.name}</h3>
                            {selectedConversation.is_group && (
                                <p className="text-xs text-grap-500">
                                    {selectedConversation.users.length} members
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}