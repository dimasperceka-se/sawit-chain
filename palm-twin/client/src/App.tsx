import { Route, Switch } from "wouter";
import PalmOilDigitalTwin from "./pages/PalmOilDigitalTwin";
import Login from "./pages/Login";

export default function App() {
  return (
    <Switch>
      <Route path="/login" component={Login} />
      <Route component={PalmOilDigitalTwin} />
    </Switch>
  );
}
