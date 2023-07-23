import fs from 'fs/promises'
import sqlite3 from 'sqlite3'
import { open } from 'sqlite';
import { exists } from './utility.js'
import { promisify } from 'util'
import { exec as callbackExec } from 'child_process'
import { dirname } from 'path'

const __dirname = dirname(import.meta.url.replace('file://', ''));
const exec = promisify(callbackExec);

// console.log(__filename);
// console.log(basename(__filename));
// console.log();


(async () => {
    console.log("Running migrations");

    const db = await open({
        filename: './database.db',
        driver: sqlite3.Database,
    })


    await db.run(`CREATE TABLE IF NOT EXISTS "migrations" ("filename" varchar,"created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP);`)
    const allMigrations = await fs.readdir('./migrations');
    const runMigrations = [...(await db.all("SELECT filename FROM migrations"))].map((row) => row.filename);

    console.log("All", allMigrations, "run", runMigrations);
    allMigrations.filter((filename) => !runMigrations.includes(filename)).forEach(async (filename) => {
        console.log(runMigrations, "filename", filename);
        const migrationSql = (await fs.readFile('./migrations/' + filename)).toString();
        await db.exec(`BEGIN; ${migrationSql}; INSERT INTO migrations (filename) VALUES ('${filename}'); COMMIT;`)
    });
    
    

    const examples = await Promise.all((await fs.readdir('examples')).map(async filename => ({
        id: filename,
        inputFilename: __dirname + '/examples/' + filename + "/input.txt",
        output: (await fs.readFile('examples/' + filename + "/output.txt")).toString(),
    })));

    // console.log(examples);

    const folders = await fs.readdir('src');

    folders.forEach(async (folder) => {
        const metadataFilename = "src/" + folder + "/metadata.json";
        if (!(await exists(metadataFilename))) return;
        
        const {language, implementations} = JSON.parse((await fs.readFile(metadataFilename)).toString());
        console.log(`Processing ${language}`)
    
        for (const [implementation, info] of Object.entries(implementations)) {
            console.log(`  Implementaton ${implementation}`)
            await db.run(
                'INSERT INTO implementation (language, implementation) VALUES (?, ?) ON CONFLICT (language, implementation) DO NOTHING',
                language,
                implementation,
            )

            const implementationRow = await db.get(
                'SELECT * FROM implementation WHERE language=? AND implementation=?',
                language,
                implementation
            );

            // We'll need to do a check on force
            const lastExecution = await db.get(
                'SELECT * FROM execution WHERE implementation_id=? ORDER BY created_at DESC',
                implementationRow.id
            );

            // console.log("Last Execution", lastExecution);
            if (lastExecution === undefined) {
                console.log("  Attempting Execution");
                const directory = __dirname + `/src/${folder}/` + (info['dir'] ?? '');
                // await exec('')
                
                if (info['build'] !== undefined) {
                    try{
                        await exec(info['build'], {
                            cwd: directory
                        })    
                    } catch (err) {
                        console.error("Unable to build project");
                        continue;
                    }
                }
                
                for (const example of examples) {
                    console.log("Executing", directory);
                    try{
                        const before = Date.now();
                        const { error, stdout, stderr } = await exec(`${info['run']} ${example.inputFilename}`, {
                            cwd: directory
                        });
                        const nodeDuration = (Date.now() - before) / 1000;
                        const trimmed = stdout.trim();

                        let output = trimmed;
                        let time = null;
                        let meta = {};

                        try{
                            const parsed = JSON.parse(trimmed);
                            output = parsed['output'] || output;
                            time = parsed['time'] || time
                            meta = parsed['meta'] || meta;
                        } catch (err) {

                        }

                        // console.log("Duration", JSON.parse(stdout.trim()));
                        await db.run(
                            `INSERT INTO execution (implementation_id, example_id, self_duration, node_duration, meta, result) VALUES(?, ?, ?, ?, ?, ?)`,
                            implementationRow['id'],
                            example['id'],
                            time,
                            nodeDuration,
                            JSON.stringify(meta),
                            output.replaceAll(" ", "") === example.output ? 1 : 0
                        );
                        console.log("Execution complete!");
                    } catch (err) {
                        console.log("Err", err)
                        continue;
                    }   
                }
            }

            // console.log(lastExecution);





            // examples.forEach()
            // if (value)
            // console.log("Value", value);




            // console.log(result);
            // const foo = await db.run(`INSERT OR     `)

            // const implementation = 
            // console.log(language, implementations);
        };
        
        // const metadata = JSON.parse("")
        // implementations.push([])
        // console.log(language);
    });

})();


// console.log(files);