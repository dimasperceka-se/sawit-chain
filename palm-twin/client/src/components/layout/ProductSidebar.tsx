import { Leaf, FlaskConical } from "lucide-react";

// Slim standalone sidebar — single product, no cross-app navigation.
export default function ProductSidebar() {
  return (
    <aside className="w-14 flex flex-col items-center py-2 shrink-0 gap-3">
      <div
        className="bg-green-600 h-10 w-10 rounded-full flex items-center justify-center shadow-md"
        title="Palm Oil Digital Twin"
      >
        <Leaf className="h-4 w-4 text-white fill-white" />
      </div>
      <div className="h-10 w-10 rounded-full flex items-center justify-center bg-gray-900 text-white shadow-md">
        <FlaskConical className="h-4 w-4" />
      </div>
    </aside>
  );
}
