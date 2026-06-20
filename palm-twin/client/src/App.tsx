import { Route, Switch, Router } from "wouter";
import PalmOilDigitalTwin from "./pages/PalmOilDigitalTwin";
import Login from "./pages/Login";

// When served under a sub-path (vite base, e.g. "/twin/"), strip the trailing
// slash so wouter route matching works. "" = served at the domain root.
const base = import.meta.env.BASE_URL.replace(/\/$/, "");

export default function App() {
  return (
    <Router base={base}>
      <Switch>
        <Route path="/login" component={Login} />
        <Route component={PalmOilDigitalTwin} />
      </Switch>
    </Router>
  );
}
