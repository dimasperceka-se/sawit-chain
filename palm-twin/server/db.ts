import pkg from "pg";

const { Pool } = pkg;

if (!process.env.DATABASE_URL) {
  throw new Error(
    "DATABASE_URL must be set. Point it at a PostGIS-enabled Postgres that has the tables peatland_idn, palmoil_mill, and plots_ady."
  );
}

export const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
});
